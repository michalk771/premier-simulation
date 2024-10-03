<template>
    <div class="final-prediction">
        <h3>Final Prediction</h3>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Team</th>
                <th>Win %</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="team in finalTable" :key="team.id">
                <td>{{ team.team.name }}</td>
                <td>{{ winPercentages[team.team.id] }}%</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['finalTable', 'winPercentages'],
};
</script>

<style scoped>
.final-prediction {
    margin-top: 20px;
}
</style>
