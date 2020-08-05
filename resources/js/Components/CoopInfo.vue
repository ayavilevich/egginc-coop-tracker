<template>
    <div>
        <h3>{{ coop.coop }}</h3>

        <p class="text-center">
            Time Left:
            <TimeLeft :seconds-left="coop.timeLeft" />
        </p>

        <div class="text-center">
            Estimate Completion:
            <TimeLeft :seconds-left="estimateCompletion" />
        </div>

        <div class="text-center">
            Projected Eggs:
            <EggFormater :eggs="projectedEggs" />
            /
            <EggFormater :eggs="eggsTotalNeeded" />
        </div>

        <div :class="{
            'red-background': !isCoopGoodToComplete,
            'green-background': isCoopGoodToComplete,
            'text-center': true,
        }">
            <EggFormater :eggs="rateNeededToComplete * 60 * 60" />
            / hr required to complete
        </div>

        <p class="text-center">
            Progress:
            <EggFormater :eggs="coop.eggs" />
            /
            <EggFormater :eggs="eggsTotalNeeded" />
        </p>

        <div>
            <progress-bar
                :val="percentDone"
                :text="percentDone + '%'"
                text-position="middle"
            />
        </div>

        <h4>Members ({{ coop.members.length }} / {{ contractInfo.maxCoopSize }})</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Name - Boost Tokens</th>
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
                        <EggFormater :eggs="member.rate * 60 * 60" />
                        / hr
                    </td>
                    <td>
                        {{ Math.round(member.eggs / totalSum * 10000) / 100 }}%
                    </td>
                    <td>
                        <EggFormater :eggs="Math.pow(10, member.soulPower) * 100" />
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>Total - {{ totalBoosts }}</td>
                    <td>
                        <EggFormater :eggs="totalSum" />
                    </td>
                    <td>
                        <EggFormater :eggs="totalRate * 60 * 60" />
                        / hr
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <hr />
    </div>
</template>

<style type="text/css">
    .red-background {
        background-color: red;
    }
    .green-background {
        background-color: #00800085;
    }
</style>

<script>
    import TimeLeft from '../Components/TimeLeft'
    import EggFormater from '../Components/EggFormater'
    import ProgressBar from 'vue-simple-progress'

    export default {
        components: {
            TimeLeft, EggFormater, ProgressBar
        },
        props: {
            coop: Object,
            contractInfo: Object,
        },
        computed: {
            percentDone() {
                return Math.round(this.coop.eggs / this.eggsTotalNeeded * 100)
            },
            eggsTotalNeeded() {
                return this.contractInfo.goalsList[this.contractInfo.goalsList.length - 1].targetAmount
            },
            eggsLeftToGet() {
                return this.eggsTotalNeeded - this.coop.eggs
            },
            totalSum() {
                let total = 0
                this.coop.members.forEach((member) => {
                    total += member.eggs
                })
                return total
            },
            totalRate() {
                let total = 0
                this.coop.members.forEach((member) => {
                    total += member.rate
                })
                return total
            },
            totalBoosts() {
                let total = 0
                this.coop.members.forEach((member) => {
                    total += member.boostTokens ? member.boostTokens : 0
                })
                return total
            },
            rateNeededToComplete() {
                return this.eggsLeftToGet / Math.abs(Math.floor(this.coop.timeLeft))
            },
            estimateCompletion() {
                let currentRateInSeconds = this.totalRate
                return Math.ceil(this.eggsLeftToGet / currentRateInSeconds)
            },
            isCoopGoodToComplete() {
                return this.rateNeededToComplete < this.totalRate
            },
            projectedEggs() {
                return this.coop.eggs + (this.totalRate * this.coop.timeLeft)
            }
        },
    }
</script>
