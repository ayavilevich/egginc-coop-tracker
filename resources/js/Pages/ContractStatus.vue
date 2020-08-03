<template>
    <layout :title="contractInfo.name + ' Status'">
        <CoopInfo v-for="(coop, index) in coopsInfo" :coop="coop" :contract-info="contractInfo" :key="index" />
    </layout>
</template>

<script>
    import Layout from './Layout'
    import CoopInfo from '../Components/CoopInfo'

    export default {
        components: {
            CoopInfo, Layout
        },
        props: {
            coopsInfo: Array,
            contractInfo: Object,
        },
    }
</script>
