<template>
    <span>
        {{ humanReadable }}
    </span>
</template>
<script>
    import MagnitudeFormat from '../magnitudeFormat.json'

    let magnitudeGet = (eggs) => {
        let last = null;
        for (let i = 0; i < MagnitudeFormat.length; i++) {
            if (eggs / Math.pow(10, MagnitudeFormat[i].magnitude) < 1) {
                break;
            }
            last = MagnitudeFormat[i]
        }
        return last
    }

    export default {
        props: {
            eggs: Number
        },
        computed: {
            humanReadable() {
                let magnitude = magnitudeGet(this.eggs)
                if (!magnitude) {
                    return this.eggs
                }

                return Math.round(this.eggs / Math.pow(10, magnitude.magnitude) * 1000) / 1000 + magnitude.symbol
            }
        }
    }
</script>
