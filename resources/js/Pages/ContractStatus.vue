<template>
    <layout :title="contractId + ' Status'">
        <div v-for="coop in coopsInfo">
            <h3>{{ coop.coop }}</h3>

            <p>Total Eggs: {{ coop.eggs }}</p>
            <p>Total Rate: {{ coop.totalRate }}</p>
            <p>Time Left: {{ coop.timeLeft }}</p>

            <h4>Members</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Egg Laid</th>
                        <th>Laying Rate</th>
                        <th>Contribution</th>
                        <th>Earning Bonus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="member in coop.members">
                        <td>
                            {{ member.name }} - {{ member.boostTokens }}
                        </td>
                        <td>{{ member.eggs }}</td>
                        <td>{{ member.rate }}</td>
                        <td>some math needs done</td>
                        <td>need to dig for this number</td>
                    </tr>
                </tbody>
            </table>
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
