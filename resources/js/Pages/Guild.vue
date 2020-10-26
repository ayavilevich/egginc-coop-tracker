<template>
    <layout :title="guild.name">
        <template v-if="!guildModel.is_bot_member_of">
            some link to add the bot to the server
        </template>

        <template v-if="guildModel.is_bot_member_of">
            <div>
                <h4>Current Contracts</h4>
                <ul>
                    <li v-for="contract in currentContracts">
                        <a :href="route('contract-guild-status', {'guildId': guild.id, 'contractId': contract.identifier})">
                            {{ contract.name }}
                        </a>
                    </li>
                </ul>
                <h4>Previous Contracts</h4>
                <ul>
                    <li>Contract 1</li>
                </ul>
            </div>

            <div>
                <h4>Player List</h4>

                <ul>
                    <li v-for="member in guildModel.members">
                        {{ member.username }}
                        (
                            <span v-for="role in member.roles">
                                {{ role.name }}
                            </span>
                        )
                    </li>
                </ul>
            </div>

            <h3>Settings</h3>
            <div>
                <form>
                    <div class="form-group">
                        <label>Admin Roles (allowed to modify coops names and users' player IDs)</label>
                        <select multiple="multiple" class="form-control">
                            <option v-for="role in guildModel.roles" :key="role.id" :value="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                    </div>
                </form>
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
            guildModel: Object,
            currentContracts: Object,
        }
    }
</script>
