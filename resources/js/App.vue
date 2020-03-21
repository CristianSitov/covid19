<template>
    <div id="app">
        <LineChart />
    </div>
</template>

<script>
    import LineChart from "./components/LineChart.vue"
    export default {
        name: "App",
        components: {
            LineChart
        },
        data() {
            return {
                height: 500,
                chartData: {
                    Books: 24,
                    Magazine: 30,
                    Newspapers: 10
                }
            };
        }
    };
</script>
