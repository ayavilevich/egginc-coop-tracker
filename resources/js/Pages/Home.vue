<template>
    <layout title="Home">
        <div class="list-group">
            <a v-for="guild in guilds" class="list-group-item list-group-item-action flex-column" href="#">
                <h5 class="mb-1">{{ guild.name }}</h5>
            </a>
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
            guilds: Object
        }
    }
</script>
