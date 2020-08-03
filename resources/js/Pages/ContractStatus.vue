<template>
    <layout :title="contractId + ' Status'">
        <div v-for="coop in coopsInfo">
            <h3>{{ coop.coop }}</h3>

            <p>
                Total Eggs:
                <EggFormater :eggs="coop.eggs" />
            </p>
            <p>
                Total Rate:
                <EggFormater :eggs="coop.totalRate" />
            </p>
            <p>
                Time Left:
                <TimeLeft :seconds-left="coop.timeLeft"></TimeLeft>
            </p>

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
                        <td>
                            <EggFormater :eggs="member.eggs" />
                        </td>
                        <td>
                            <EggFormater :eggs="member.rate" />
                            / hr
                        </td>
                        <td>coming soon</td>
                        <td>coming soon</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td>
                            <EggFormater :eggs="totalSum(coop)" />
                        </td>
                        <td>
                            <EggFormater :eggs="totalRate(coop)" />
                            / hr
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <hr />
        </div>
    </layout>
</template>

<script>
    import Layout from './Layout'
    import TimeLeft from '../Components/TimeLeft'
    import EggFormater from '../Components/EggFormater'

    export default {
        components: {
            Layout, TimeLeft, EggFormater
        },
        props: {
            contractId: String,
            coopsInfo: Array,
        },
        methods: {
            totalSum(coop) {
                let total = 0
                coop.members.forEach((member) => {
                    total += member.eggs
                })
                return total
            },
            totalRate(coop) {
                let total = 0
                coop.members.forEach((member) => {
                    total += member.rate
                })
                return total
            }
        },
    }
</script>
