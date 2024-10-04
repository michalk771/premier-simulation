<template>
    <div>
        <h1>Insider Champions League Simulation</h1>
        <LeagueTable :leagueTable="leagueTable" :currentWeek="currentWeek" />
        <MatchResults :weekMatches="weekMatches" :currentWeek="currentWeek" />
        <FinalPrediction v-if="currentWeek >= 1" :finalTable="finalTable" :winPercentages="winPercentages" />
        <div class="buttons">
            <button @click="simulateWeek">Simulate Next Week</button>
        </div>
    </div>
</template>

<script>
import LeagueTable from './PremierLeague/LeagueTable.vue';
import MatchResults from './PremierLeague/MatchResults.vue';
import FinalPrediction from './PremierLeague/FinalPrediction.vue';
import axios from 'axios';

export default {
    components: { LeagueTable, MatchResults, FinalPrediction },
    data() {
        return {
            currentWeek: 1,
            leagueTable: [],
            weekMatches: [],
            finalTable: [],
            winPercentages: {}
        };
    },
    mounted() {
        this.loadLatestWeeks();
    },
    methods: {
        async loadLatestWeeks() {
            const response = await axios.get('/latest-weeks');
            this.weekMatches = response.data.weekMatches;
            this.finalTable = response.data.finalTable;
            this.leagueTable = response.data.leagueTable;
            this.winPercentages = response.data.winPercentages;
            this.currentWeek = response.data.weekNumber;
        },
        async simulateWeek() {
            const response = await axios.post('/simulate-week', {
                week: this.currentWeek + 1
            });

            this.currentWeek++;
            await this.loadLatestWeeks();
        }
    }
};
</script>

<style lang="scss">
div {
    margin: 20px;
}

h1 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}
td {
    padding: 10px;
}
.table {
    margin-top: 10px;
    border-collapse: collapse;
    width: 100%;
    font-family: 'Arial', sans-serif;

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
}

.buttons {
    margin-top: 20px;

    button {
        margin-right: 10px;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;

        &:disabled {
            background-color: grey;
            cursor: not-allowed;
        }

        &:hover {
            background-color: #45a049;
        }
    }
}
</style>
