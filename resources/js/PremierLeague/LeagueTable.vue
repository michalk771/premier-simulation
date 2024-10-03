<template>
    <div class="league-table">
        <h3>League Table After Week {{ currentWeek }}</h3>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Team</th>
                <th>Played</th>
                <th>Won</th>
                <th>Drawn</th>
                <th>Lost</th>
                <th>Goals For</th>
                <th>Goals Against</th>
                <th>Goal Difference</th>
                <th>Points</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="team in leagueTable" :key="team.id">
                <td>{{ team.team.name }}</td>
                <td>{{ team.played }}</td>
                <td>{{ team.won }}</td>
                <td>{{ team.drawn }}</td>
                <td>{{ team.lost }}</td>
                <td>{{ team.goals_for }}</td>
                <td>{{ team.goals_against }}</td>
                <td>{{ team.goal_difference }}</td>
                <td>{{ team.points }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['leagueTable', 'currentWeek'],
};
</script>

<style scoped>
.league-table {
    margin-top: 20px;
}
</style>
