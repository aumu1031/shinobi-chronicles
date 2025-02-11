<?php
/**
 * @var System $system
 * @var User $player
 * @var Layout $layout
 */
?>

<link rel="stylesheet" type="text/css" href="<?= $system->getCssFileLink("ui_components/src/profile/Profile.css") ?>" />
<div id="profileReactContainer"></div>
<script type="module" src="<?= $system->getReactFile("profile/Profile") ?>"></script>

<!-- Chart.js - required for {MyChart} Component in Profile.js -->
<script src="
https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js
"></script>

<!--suppress JSUnresolvedVariable, JSUnresolvedFunction -->
<script type="text/javascript">
    const profileContainer = document.querySelector("#profileReactContainer");

    window.addEventListener('load', () => {
        ReactDOM.render(
            React.createElement(Profile, {
                isDevEnvironment: Boolean(<?= (int)$system->isDevEnvironment() ?>),
                links: {
                    clan: "<?= $system->router->getUrl('clan') ?>",
                    team: "<?= $system->router->getUrl('team') ?>",
                    bloodlinePage: "<?= $system->router->getUrl('bloodline') ?>",
                    buyBloodline: "<?= $system->router->getUrl('premium', ['view' => 'bloodlines']) ?>",
                    buyForbiddenSeal: "<?= $system->router->getUrl('premium', ['view' => 'forbidden_seal']) ?>",
                },
                playerData: <?= json_encode(
                    UserAPIPresenter::playerDataResponse(player: $player, rank_names: RankManager::fetchNames($system))
                ) ?>,
                playerStats: <?= json_encode(UserApiPresenter::playerStatsResponse($player)) ?>,
                playerSettings: <?= json_encode(UserAPIPresenter::playerSettingsResponse($player)) ?>,
                playerDailyTasks: <?= json_encode(UserApiPresenter::dailyTasksResponse($player->daily_tasks->tasks)) ?>,
                playerAchievements: <?= json_encode(UserApiPresenter::playerAchievementsResponse($player)) ?>,
            }),
            profileContainer
        );
    })
</script>
