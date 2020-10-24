<template>
    <layout :title="guild.name">
        <template v-if="!guildModel.is_bot_member_of">
            some link to add the bot to the server
        </template>

        <template v-if="guildModel.is_bot_member_of">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <h4>Current Contracts</h4>
                    </div>
                    <div class="row">
                        <h4>Previous Contracts</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4>Player List</h4>

                    <ul>
                        <li v-for="member in guildModel.members">
                            {{ member.username }}
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </layout>
</template>

<script>
    import Layout from './Layout'

    export default {
        components: {
            Layout,
        },
        props: {
            guild: Object,
            guildModel: Object
        }
    }
</script>
