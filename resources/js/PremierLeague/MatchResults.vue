<template>
    <div class="match-results">
        <h3>Match Results - Week {{ currentWeek }}</h3>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Home Team</th>
                <th>Score</th>
                <th>Away Team</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="match in weekMatches " :key="match.id">
                <td>{{ match.home_team.name }}</td>
                <td>{{ match.home_score }} - {{ match.away_score }}</td>
                <td>{{ match.away_team.name }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['weekMatches', 'currentWeek'],
};
</script>

<style scoped>
.match-results {
    margin-top: 20px;
}
</style>
