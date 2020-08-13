<template>
    <span>
        <template v-if="daysLeft">
            {{ daysLeft }} {{ daysLeft > 1 ? 'days' : 'day' }}
        </template>
        <template v-if="hoursLeft">
            {{ hoursLeft }} {{ hoursLeft > 1 ? 'hours' : 'hour' }}
        </template>
        {{ minutesLeft}} {{ minutesLeft > 1 ? 'minutes' : 'minute' }}
    </span>
</template>

<script>
    export default {
        props: {
            secondsLeft: Number
        },
        computed: {
            daysLeft() {
                return Math.floor(this.secondsLeft / (60 * 60 * 24))
            },
            hoursLeft() {
                return Math.abs(Math.floor(this.secondsLeft / (60 * 60) - (this.daysLeft * 24)))
            },
            minutesLeft() {
                return Math.abs(Math.floor(this.secondsLeft / 60 - (this.daysLeft * 24 * 60) - (this.hoursLeft * 60)))
            },
        }
    }
</script>
