<script>
    import { Line } from "vue-chartjs";

    export default {
        extends: Line,
        mounted() {
            let uri = 'http://covid-19.test/storage/numbers.json';
            let data = null;
            axios.get(uri).then((response) => {
                data = response.data;
                if (data) {
                    this.renderChart(
                        data,
                        {
                            responsive: true,
                            maintainAspectRatio: true,
                            showAllTooltips: true,
                            title: {
                                display: true,
                                text: "COVID-19"
                            }
                        }
                    );
                } else {
                    console.log('No data');
                }
            });
        }
    };
</script>
