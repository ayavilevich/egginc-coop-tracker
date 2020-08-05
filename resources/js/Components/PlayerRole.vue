<template>
    <span>
        {{ humanReadable }}
    </span>
</template>
<script>
    import MagnitudeFormat from '../roleMagnitude.json'

    let magnitudeGet = (soulPower) => {
        let last = null;
        for (let i = 0; i < MagnitudeFormat.length; i++) {
            if (soulPower / Math.pow(10, MagnitudeFormat[i].magnitude) < 1) {
                break;
            }
            last = MagnitudeFormat[i]
        }
        return last
    }

    export default {
        props: {
            soulPower: Number
        },
        computed: {
            humanReadable() {
                let magnitude = magnitudeGet(Math.pow(10, this.soulPower) * 100)
                if (!magnitude) {
                    return ''
                }

                return magnitude.name
            }
        }
    }
</script>
