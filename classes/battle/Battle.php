<?php

require_once __DIR__ . '/BattleEffectsManager.php';
require_once __DIR__ . '/BattleLog.php';
require_once __DIR__ . '/../war/Operation.php';

class Battle {
    const TYPE_AI_ARENA = 1;
    const TYPE_SPAR = 2;
    const TYPE_FIGHT = 3;
    const TYPE_CHALLENGE = 4;
    const TYPE_AI_MISSION = 5;
    const TYPE_AI_RANKUP = 6;
    const TYPE_AI_WAR = 7;

    const TURN_LENGTH = 15;
    const INITIAL_TURN_LENGTH = 40;
    const PREP_LENGTH = 20;

    const MAX_PRE_FIGHT_HEAL_PERCENT = 85;

    const TEAM1 = 'T1';
    const TEAM2 = 'T2';
    const DRAW = 'DRAW';
    const STOP = 'STOP';

    // Minimum % (of itself) a debuff can be reduced to with debuff resist
    const MIN_DEBUFF_RATIO = 0.1;
    const MAX_DIFFUSE_PERCENT = 0.75;

    const REPUTATION_DAMAGE_RESISTANCE_BOOST = 15;

    private System $system;

    public string $raw_active_effects;
    public string $raw_active_genjutsu;
    public string $raw_field;

    // Properties
    public int $battle_id;
    public int $battle_type;

    public int $start_time;
    public int $turn_time;
    public int $turn_count;
    public int $player1_time;
    public int $player2_time;

    public string $winner;
    public bool $is_retreat = false;

    public Fighter $player;

    public string $player1_id;
    public string $player2_id;

    public Fighter $player1;
    public Fighter $player2;

    public array $fighter_health;

    /** @var FighterAction[] */
    public array $fighter_actions;

    /** @var BattleLog[] */
    public array $log;

    public array $jutsu_cooldowns;

    /** @var array [user_combat_id][jutsu_combat_id] => [count, jutsu_type] */
    public array $fighter_jutsu_used;

    // transient instance var - more convenient to interface with log this way for the moment
    public string $battle_text;

    public ?int $patrol_id;

    /**
     * @param System  $system
     * @param Fighter $player1
     * @param Fighter $player2
     * @param int     $battle_type
     * @return mixed
     * @throws RuntimeException
     */
    public static function start(
        System $system, Fighter $player1, Fighter $player2, int $battle_type, ?int $patrol_id = null
    ) {
        $json_empty_array = '[]';

        switch($battle_type) {
            case self::TYPE_AI_ARENA:
            case self::TYPE_SPAR:
            case self::TYPE_FIGHT:
            case self::TYPE_CHALLENGE:
            case self::TYPE_AI_MISSION:
            case self::TYPE_AI_RANKUP:
            case self::TYPE_AI_WAR:
                break;
            default:
                throw new RuntimeException("Invalid battle type!");
        }

        $player1->combat_id = Battle::combatId(Battle::TEAM1, $player1);
        $player2->combat_id = Battle::combatId(Battle::TEAM2, $player2);

        $fighter_health = [
            $player1->combat_id => $player1->health,
            $player2->combat_id => $player2->health
        ];

        $system->db->query(
            "INSERT INTO `battles` SET
                `battle_type` = '" . $battle_type . "',
                `start_time` = '" . time() . "',
                `turn_time` = '" . (time() + self::PREP_LENGTH - 5) . "',
                `player1_time` = '" . self::INITIAL_TURN_LENGTH . "',
                `player2_time` = '" . self::INITIAL_TURN_LENGTH . "',
                `turn_count` = '" . 0 . "',
                `winner` = '',
                `player1` = '" . $player1->id . "',
                `player2` = '" . $player2->id . "',
                `fighter_health` = '" . json_encode($fighter_health) . "',
                `fighter_actions` = '" . $json_empty_array . "',
                `field` = '" . $json_empty_array . "',
                `active_effects` = '" . $json_empty_array . "',
                `active_genjutsu` = '" . $json_empty_array . "',
                `jutsu_cooldowns` = '" . $json_empty_array . "',
                `fighter_jutsu_used` = '" . $json_empty_array . "',
                `is_retreat` = '" . (int)false . "',
                `patrol_id` = " . (!empty($patrol_id) ? $patrol_id : "NULL") . "
        ");
        $battle_id = $system->db->last_insert_id;

        if($player1 instanceof User) {
            $player1->battle_id = $battle_id;
            if ($battle_type == self::TYPE_FIGHT) {
                $player1->pvp_immunity_ms = 0; // if attacking lose immunity
                $system->db->query("UPDATE `loot` SET `battle_id` = {$battle_id} WHERE `user_id` = {$player1->user_id}");
            }
            if ($player1->operation > 0) {
                Operation::cancelOperation($system, $player1);
            }

            $player1->updateData();
        }
        if($player2 instanceof User) {
            $player2->battle_id = $battle_id;
            if ($battle_type == self::TYPE_FIGHT) {
                $system->db->query("UPDATE `loot` SET `battle_id` = {$battle_id} WHERE `user_id` = {$player2->user_id}");
            }
            if ($player2->operation > 0) {
                Operation::cancelOperation($system, $player2);
            }
            $player2->updateData();
        }

        // Create Notifications
        if ($battle_type == self::TYPE_FIGHT) {
            $new_notification = new BattleNotificationDto(
                action_url: $system->router->getUrl('battle'),
                type: "battle",
                message: "In battle!",
                user_id: $player1->user_id,
                created: time(),
                battle_id: $battle_id,
                alert: false,
            );
            NotificationManager::createNotification($new_notification, $system, NotificationManager::UPDATE_REPLACE);
            $new_notification = new BattleNotificationDto(
                action_url: $system->router->getUrl('battle'),
                type: "battle",
                message: "In battle!",
                user_id: $player2->user_id,
                created: time(),
                battle_id: $battle_id,
                alert: true,
            );
            NotificationManager::createNotification($new_notification, $system, NotificationManager::UPDATE_REPLACE);
        }

        return $battle_id;
    }

    /**
     * Battle constructor.
     * @param System $system
     * @param User   $player
     * @param int    $battle_id
     * @throws RuntimeException
     */
    public function __construct(
        System $system, User $player, int $battle_id
    ) {
        $this->system = $system;

        $this->battle_id = $battle_id;
        $this->player = $player;

        $result = $this->system->db->query(
            "SELECT * FROM `battles` WHERE `battle_id`='{$battle_id}' LIMIT 1 FOR UPDATE"
        );
        if($this->system->db->last_num_rows == 0) {
            if($player->battle_id = $battle_id) {
                $player->battle_id = 0;
            }
            throw new RuntimeException("Invalid battle!");
        }

        $battle = $this->system->db->fetch($result);

        $this->raw_active_effects = $battle['active_effects'];
        $this->raw_active_genjutsu = $battle['active_genjutsu'];
        $this->raw_field = $battle['field'];

        $this->battle_type = $battle['battle_type'];

        $this->start_time = $battle['start_time'];
        $this->turn_time = $battle['turn_time'];
        $this->player1_time = $battle['player1_time'];
        $this->player2_time = $battle['player2_time'];
        $this->turn_count = $battle['turn_count'];

        $this->winner = $battle['winner'];
        if (isset($battle['is_retreat'])) {
            $this->is_retreat = $battle['is_retreat'];
        }

        $this->player1_id = $battle['player1'];
        $this->player2_id = $battle['player2'];

        $this->fighter_health = json_decode($battle['fighter_health'], true);
        $this->fighter_actions = array_map(function($action_data) {
            return LegacyFighterAction::fromDb($action_data);
        }, json_decode($battle['fighter_actions'], true));

        $this->jutsu_cooldowns = json_decode($battle['jutsu_cooldowns'] ?? "[]", true);

        $this->fighter_jutsu_used = json_decode($battle['fighter_jutsu_used'], true);

        $this->patrol_id = $battle['patrol_id'];

        // lo9g
        $last_turn_log = BattleLog::getLastTurn($this->system, $this->battle_id);
        if($last_turn_log != null) {
            $this->log[$last_turn_log->turn_number] = $last_turn_log;
            $this->battle_text = $last_turn_log->content;
        }
        else {
            $this->battle_text = '';
        }
    }

    /**
     * @throws RuntimeException
     */
    public function loadFighters() {
        if($this->player1_id != $this->player->id) {
            $this->player1 = $this->loadFighterFromEntityId($this->player1_id);
        }
        if($this->player2_id != $this->player->id) {
            $this->player2 = $this->loadFighterFromEntityId($this->player2_id);
        }

        $this->player1->combat_id = Battle::combatId(Battle::TEAM1, $this->player1);
        $this->player2->combat_id = Battle::combatId(Battle::TEAM2, $this->player2);

        if($this->player1 instanceof NPC) {
            $this->player1->loadData();
            $this->player1->health = $this->fighter_health[$this->player1->combat_id];
        }
        if($this->player2 instanceof NPC) {
            $this->player2->loadData();
            $this->player2->health = $this->fighter_health[$this->player2->combat_id];
        }

        if($this->player1 instanceof User && $this->player1->id != $this->player->id) {
            $this->player1->loadData(User::UPDATE_NOTHING, true);
        }
        if($this->player2 instanceof User && $this->player2->id != $this->player->id) {
            $this->player2->loadData(User::UPDATE_NOTHING, true);
        }
        if ($this->battle_type == Battle::TYPE_CHALLENGE) {
		    if (true) {
			    $tier_difference = $this->player1->reputation->rank - $this->player2->reputation->rank;
			    if ($tier_difference > 0) {
				    $this->player1->reputation_defense_boost = Battle::REPUTATION_DAMAGE_RESISTANCE_BOOST * abs($tier_difference);
			    }
                else if ($tier_difference < 0) {
                    $this->player2->reputation_defense_boost = Battle::REPUTATION_DAMAGE_RESISTANCE_BOOST * abs($tier_difference);
                }
		    }
	    }

        $this->player1->getInventory();
        $this->player2->getInventory();

        $this->player1->applyBloodlineBoosts();
        $this->player2->applyBloodlineBoosts();
    }

    /**
     * @param string $entity_id
     * @return Fighter
     * @throws RuntimeException
     */
    protected function loadFighterFromEntityId(string $entity_id): Fighter {
        switch(Battle::getFighterEntityType($entity_id)) {
            case User::ENTITY_TYPE:
                return User::fromEntityId($this->system, $entity_id);
            case NPC::ID_PREFIX:
                return NPC::fromEntityId($this->system, $entity_id);
            default:
                throw new RuntimeException("Invalid entity type! " . Battle::getFighterEntityType($entity_id));
        }
    }

    /**
     * @param string $entity_id
     * @return string
     * @throws RuntimeException
     */
    protected static function getFighterEntityType(string $entity_id): string {
        $entity_id = System::parseEntityId($entity_id);
        return $entity_id->entity_type;
    }

    public function isComplete(): bool {
        return $this->winner;
    }

    public function isPreparationPhase(): bool {
        return $this->prepTimeRemaining() > 0 && in_array($this->battle_type, [Battle::TYPE_FIGHT]);
    }

    /**
     * @throws RuntimeException
     */

    public function timeRemaining($player_id = 0): int {
        // If not set, use current player
        if ($player_id == 0) {
            $player_id = $this->player->id;
        }
        // If action is set, player's turn time is frozen - else modify by time since last turn
        switch ($player_id) {
            case $this->player1_id:
                if (isset($this->fighter_actions[$this->player1->combat_id])) {
                    return $this->player1_time;
                }
                return $this->player1_time - (time() - $this->turn_time);
            case $this->player2_id:
                if (isset($this->fighter_actions[$this->player2->combat_id])) {
                    return $this->player2_time;
                }
                return $this->player2_time - (time() - $this->turn_time);
            default:
                return 0;
        }
    }

    public function prepTimeRemaining(): int {
        return Battle::PREP_LENGTH - (time() - $this->start_time);
    }

    public function updatePlayerTime($player_id = 0, $min = false) {
        // if min, set to 15 seconds - else add 15 seconds and bound to min/max turn time
        switch ($player_id) {
            case $this->player1_id:
                if ($min) {
                    $this->player1_time = self::TURN_LENGTH;
                } else {
                    $this->player1_time = min(max(($this->player1_time - (time() - $this->turn_time)) + self::TURN_LENGTH, self::TURN_LENGTH), self::INITIAL_TURN_LENGTH);
                }
                break;
            case $this->player2_id:
                if ($min) {
                    $this->player2_time = self::TURN_LENGTH;
                } else {
                    $this->player2_time = min(max(($this->player2_time - (time() - $this->turn_time)) + self::TURN_LENGTH, self::TURN_LENGTH), self::INITIAL_TURN_LENGTH);
                }
                break;
            default:
                break;
        }
    }

    public function updateData() {
        $this->system->db->query("START TRANSACTION;");
        $is_retreat = (int)$this->is_retreat;
        $this->system->db->query(
            "UPDATE `battles` SET
            `turn_time` = {$this->turn_time},
            `turn_count` = {$this->turn_count},
            `winner` = '{$this->winner}',
            `is_retreat` = {$is_retreat},

            `player1_time` = {$this->player1_time},
            `player2_time` = {$this->player2_time},

            `fighter_health` = '" . json_encode($this->fighter_health) . "',
            `fighter_actions` = '" . json_encode($this->fighter_actions) . "',

            `field` = '" . $this->raw_field . "',

            `active_effects` = '" . $this->raw_active_effects . "',
            `active_genjutsu` = '" . $this->raw_active_genjutsu . "',

            `jutsu_cooldowns` = '" . json_encode($this->jutsu_cooldowns) . "',
            `fighter_jutsu_used` = '" . json_encode($this->fighter_jutsu_used) . "'
            WHERE `battle_id` = '{$this->battle_id}' LIMIT 1"
        );

        BattleLog::addOrUpdateTurnLog($this->system, $this->battle_id, $this->turn_count, $this->battle_text);

        $this->system->db->query("COMMIT;");
    }

    public static function combatId(string $team, Fighter $fighter): string {
        return $team . ':' . $fighter->id;
    }
}
