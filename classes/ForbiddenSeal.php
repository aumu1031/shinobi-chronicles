<?php

class ForbiddenSeal {
    const ONE_MEGABYTE = 1024 ** 2;

    public System $system;
    public int $level;
    public int $seal_end_time;
    public int $seal_time_remaining;

    /** Benefits **/
    public string $name;
    public array $name_colors;

    public int $regen_boost;

    public int $avatar_size;
    public int $avatar_filesize;
    public array $avatar_styles;

    public int $logout_timer;
    public int $inbox_size;

    public int $journal_size;
    public int $journal_image_x;
    public int $journal_image_y;
    public bool $journal_youtube_embed;

    public int $chat_post_size;
    public int $pm_size;

    public int $extra_jutsu_equips;
    public int $extra_armor_equips;
    public int $extra_weapon_equips;

    public int $stat_transfer_boost;
    public int $extra_stat_transfer_points_per_ak;
    public int $free_transfer_bonus;

    public float $long_training_time;
    public float $long_training_gains;
    public float $extended_training_time;
    public float $extended_training_gains;

    public int $max_battle_history_view;
    public int $bonus_pve_reputation;

    /** Display Members (used only in Ancient Market) */
    public string $name_color_display;
    public string $avatar_size_display;
    public string $journal_image_display;

    /** Static Members **/
    public static int $STAFF_SEAL_LEVEL = 2; //Defines default benefits for staff members (Note: Not all benefits are provided)
    public static int $SECONDS_IN_DAY = 86400;
    public static array $forbidden_seal_names = array(
        0 => '',
        1 => 'Twin Sparrow Seal',
        2 => 'Four Dragon Seal',
        3 => 'Eight Deities Seal'
    );
    public static array $benefits = array (
        0 => [
            'regen_boost' => 0,
            'name_colors' => [],
            'name_color_display' => 'Standard',
            'avatar_size' => 125,
            'avatar_size_display' => '125x125',
            'avatar_filesize' => self::ONE_MEGABYTE,
            'avatar_styles' => [
                'avy_none' => 'none',
                'avy_round' => 'circle',
                'avy_four-point' => 'square',
            ],
            'logout_timer' => System::LOGOUT_LIMIT,
            'inbox_size' => 50,
            'journal_size' => 1000,
            'journal_image_x' => 300,
            'journal_image_y' => 200,
            'journal_image_display' => '300x200',
            'journal_youtube_embed' => false,
            'chat_post_size' => 350,
            'pm_size' => 1000,
            'extra_jutsu_equips' => 0,
            'extra_weapon_equips' => 0,
            'extra_armor_equips' => 0,
            'long_training_time' => 1,
            'long_training_gains' => 1,
            'extended_training_time' => 1,
            'extended_training_gains' => 1,
            'stat_transfer_boost' => 0,
            'extra_stat_transfer_points_per_ak' => 0,
            'max_battle_history_view' => 0,
            'free_transfer_bonus' => 0,
            'bonus_pve_reputation' => 0,
        ],
        1 => [
            'regen_boost' => 10,
            'name_colors' => [
                'blue' => 'blue',
                'pink' => 'pink',
            ],
            'name_color_display' => 'Blue/Pink', //Premium page display only.
            'avatar_size' => 200,
            'avatar_size_display' => '200x200', //Premium page display only.
            'avatar_filesize' => self::ONE_MEGABYTE,
            'avatar_styles' => [
                'avy_none' => 'none',
                'avy_round' => 'circle',
                'avy_three-point' => 'triangle',
                'avy_three-point-inverted' => 'triange inverted',
                'avy_four-point' => 'square',
                'avy_four-point-90' => 'diamond',
                'avy_four-point-oblique' => 'oblique',
                'avy_five-point' => 'pentagon',
                'avy_six-point' => 'hexagon',
                'avy_six-point-long' => 'crystal',
                //'avy_six-point-long-reverse' => 'crystal reverse',
                'avy_eight-point' => 'octagon',
                'avy_eight-point-wide' => 'octagon wide',
                'avy_nine-point' => 'nonagon',
                'avy_twelve-point' => 'cross',
            ],
            'logout_timer' => System::LOGOUT_LIMIT,
            'inbox_size' => 75,
            'journal_size' => 2000,
            'journal_image_x' => 500,
            'journal_image_y' => 500,
            'journal_image_display' => '500x500', //Premium page display only.
            'journal_youtube_embed' => false,
            'chat_post_size' => 450,
            'pm_size' => 1500,
            'extra_jutsu_equips' => 0,
            'extra_weapon_equips' => 0,
            'extra_armor_equips' => 0,
            'long_training_time' => 1,
            'long_training_gains' => 1,
            'extended_training_time' => 1,
            'extended_training_gains' => 1,
            'stat_transfer_boost' => 0,
            'extra_stat_transfer_points_per_ak' => 50,
            'max_battle_history_view' => 10,
            'free_transfer_bonus' => 25,
            'bonus_pve_reputation' => 0,
        ],
        2 => [
            'regen_boost' => 20, //Report in whole percentages (20 will be .2 bonus)
            'name_colors' => [
                'blue' => 'blue',
                'pink' => 'pink',
            ],
            'name_color_display' => 'Blue/Pink', //Premium page desc display only
            'avatar_size' => 200,
            'avatar_size_display' => '200x200', //Premium page display only.
            'avatar_filesize' => self::ONE_MEGABYTE * 2,
            'avatar_styles' => [
                'avy_none' => 'none',
                'avy_round' => 'circle',
                'avy_three-point' => 'triangle',
                'avy_three-point-inverted' => 'triange inverted',
                'avy_four-point' => 'square',
                'avy_four-point-90' => 'diamond',
                'avy_four-point-oblique' => 'oblique',
                'avy_five-point' => 'pentagon',
                'avy_six-point' => 'hexagon',
                'avy_six-point-long' => 'crystal',
                //'avy_six-point-long-reverse' => 'crystal reverse',
                'avy_eight-point' => 'octagon',
                'avy_eight-point-wide' => 'octagon wide',
                'avy_nine-point' => 'nonagon',
                'avy_twelve-point' => 'cross',
            ],
            'logout_timer' => System::LOGOUT_LIMIT,
            'inbox_size' => 75,
            'journal_size' => 2500,
            'journal_image_x' => 500,
            'journal_image_y' => 500,
            'journal_image_display' => '500x500', //Premium page display only.
            'journal_youtube_embed' => false,
            'chat_post_size' => 450,
            'pm_size' => 1500,
            'extra_jutsu_equips' => 1,
            'extra_weapon_equips' => 1,
            'extra_armor_equips' => 1,
            'long_training_time' => 1.5,
            'long_training_gains' => 2,
            'extended_training_time' => 1.5,
            'extended_training_gains' => 2,
            'stat_transfer_boost' => 5,
            'extra_stat_transfer_points_per_ak' => 100,
            'max_battle_history_view' => 20,
            'free_transfer_bonus' => 50,
            'bonus_pve_reputation' => 1,
        ],
        3 => [
            'regen_boost' => 30, //Report in whole percentages (20 will be .2 bonus)
            'name_colors' => [
                'blue' => 'blue',
                'pink' => 'pink',
            ],
            'name_color_display' => 'Blue/Pink', //Premium page desc display only
            'avatar_size' => 200,
            'avatar_size_display' => '200x200', //Premium page display only.
            'avatar_filesize' => self::ONE_MEGABYTE * 2,
            'avatar_styles' => [
                'avy_none' => 'none',
                'avy_round' => 'circle',
                'avy_three-point' => 'triangle',
                'avy_three-point-inverted' => 'triange inverted',
                'avy_four-point' => 'square',
                'avy_four-point-90' => 'diamond',
                'avy_four-point-oblique' => 'oblique',
                'avy_five-point' => 'pentagon',
                'avy_six-point' => 'hexagon',
                'avy_six-point-long' => 'crystal',
                //'avy_six-point-long-reverse' => 'crystal reverse',
                'avy_eight-point' => 'octagon',
                'avy_eight-point-wide' => 'octagon wide',
                'avy_nine-point' => 'nonagon',
                'avy_twelve-point' => 'cross',
            ],
            'logout_timer' => System::LOGOUT_LIMIT,
            'inbox_size' => 75,
            'journal_size' => 3500,
            'journal_image_x' => 600,
            'journal_image_y' => 600,
            'journal_image_display' => '600x600', //Premium page display only.
            'journal_youtube_embed' => true,
            'chat_post_size' => 450,
            'pm_size' => 1500,
            'extra_jutsu_equips' => 1,
            'extra_weapon_equips' => 1,
            'extra_armor_equips' => 1,
            'long_training_time' => 1.5,
            'long_training_gains' => 2.2,
            'extended_training_time' => 2,
            'extended_training_gains' => 3,
            'stat_transfer_boost' => 10,
            'extra_stat_transfer_points_per_ak' => 150,
            'max_battle_history_view' => 50,
            'free_transfer_bonus' => 50,
            'bonus_pve_reputation' => 1,
        ]
    );

    public function __construct(System $system, int $seal_level, int $seal_end_time = 0) {
        if(!isset(self::$forbidden_seal_names[$seal_level])) {
            $seal_level = 0;
        }
        $this->system = $system;
        $this->level = $seal_level;
        $this->name = self::$forbidden_seal_names[$this->level];
        $this->seal_end_time = $seal_end_time;
        $this->seal_time_remaining = $this->seal_end_time - time();
    }

    public function checkExpiration() {
        // Seal expired, remove the seal
        if($this->level > 0 && time() > $this->seal_end_time) {
            $this->system->message("Your " . self::$forbidden_seal_names[$this->level] . " has expired!");
            $this->level = 0;
            $this->setBenefits();
        }
    }

    public function addSeal(int $seal_level, int $days_to_add) {
        //Add time
        if($seal_level == $this->level) {
            $this->seal_end_time += $days_to_add * self::$SECONDS_IN_DAY;
            $this->seal_time_remaining += $days_to_add * self::$SECONDS_IN_DAY;
        }
        //Overwrite seal
        else {
            $this->level = $seal_level;
            $this->name = self::$forbidden_seal_names[$this->level];
            $this->seal_end_time = time() + ($days_to_add * self::$SECONDS_IN_DAY);
            $this->seal_time_remaining = $this->seal_end_time - time();
        }
    }

    public function dbEncode() {
        switch($this->level) {
            case 0:
                return "";
            default:
                return json_encode(array('level' => $this->level, 'time' => $this->seal_end_time));
        }
    }

    public function setBenefits() {
        $this->regen_boost = self::$benefits[$this->level]['regen_boost'];
        $this->name_colors = self::$benefits[$this->level]['name_colors'];
        $this->avatar_size = self::$benefits[$this->level]['avatar_size'];
        $this->avatar_filesize = self::$benefits[$this->level]['avatar_filesize'];
        $this->avatar_styles = self::$benefits[$this->level]['avatar_styles'];
        $this->logout_timer = self::$benefits[$this->level]['logout_timer'];
        $this->inbox_size = self::$benefits[$this->level]['inbox_size'];
        $this->journal_size = self::$benefits[$this->level]['journal_size'];
        $this->journal_image_x = self::$benefits[$this->level]['journal_image_x'];
        $this->journal_image_y = self::$benefits[$this->level]['journal_image_y'];
        $this->journal_youtube_embed = self::$benefits[$this->level]['journal_youtube_embed'];
        $this->chat_post_size = self::$benefits[$this->level]['chat_post_size'];
        $this->pm_size = self::$benefits[$this->level]['pm_size'];
        $this->extra_jutsu_equips = self::$benefits[$this->level]['extra_jutsu_equips'];
        $this->extra_weapon_equips = self::$benefits[$this->level]['extra_weapon_equips'];
        $this->extra_armor_equips = self::$benefits[$this->level]['extra_armor_equips'];
        $this->long_training_time = self::$benefits[$this->level]['long_training_time'];
        $this->long_training_gains = self::$benefits[$this->level]['long_training_gains'];
        $this->extended_training_time = self::$benefits[$this->level]['extended_training_time'];
        $this->extended_training_gains = self::$benefits[$this->level]['extended_training_gains'];
        $this->stat_transfer_boost = self::$benefits[$this->level]['stat_transfer_boost'];
        $this->extra_stat_transfer_points_per_ak = self::$benefits[$this->level]['extra_stat_transfer_points_per_ak'];
        $this->max_battle_history_view = self::$benefits[$this->level]['max_battle_history_view'];
        $this->free_transfer_bonus = self::$benefits[$this->level]['free_transfer_bonus'];
        $this->bonus_pve_reputation = self::$benefits[$this->level]['bonus_pve_reputation'];

        // Display variables
        $this->name_color_display = self::$benefits[$this->level]['name_color_display'];
        $this->journal_image_display = self::$benefits[$this->level]['journal_image_display'];
        $this->avatar_size_display = self::$benefits[$this->level]['avatar_size_display'];
    }

    public function calcRemainingCredit(): int {
        $days_per_ak = 1;
        switch($this->level) {
            case 1:
                $days_per_ak = 6;
                break;
            case 2:
                $days_per_ak = 2;
                break;
            default:
        }
        return floor(floor($this->seal_time_remaining / 86400) / $days_per_ak);
    }
}