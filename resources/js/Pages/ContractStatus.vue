<template>
    <layout :title="contractId + ' Status'">
        <div v-for="coop in coopsInfo">
            <h3>{{ coop.coop }}</h3>

            <p>Total Eggs: {{ coop.eggs }}</p>
            <p>Total Rate: {{ coop.totalRate }}</p>
            <p>Time Left: {{ coop.timeLeft }}</p>

            <h4>Members</h4>
            <hr />
        </div>
    </layout>
</template>

<script>
    import Layout from './Layout'

    export default {
        components: {
            Layout,
        },
        props: {
            contractId: String,
            coopsInfo: Object,
        }
    }
</script>
