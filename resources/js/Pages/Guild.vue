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

                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Roles</th>
                            <th>Earning Bonus (Rank)</th>
                            <th>Soul Eggs (Golden Eggs)</th>
                            <th>Drones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in guildModel.members">
                            <td>{{ member.username }}</th>
                            <td>
                                <ul>
                                    <li v-for="role in member.roles">
                                        {{ role.name }}
                                    </li>
                                </ul>
                            </td>
                            <td>
                                {{ member.player_earning_bonus_formatted }}
                                ({{ member.player_egg_rank }})
                            </td>
                            <td>
                                <EggFormater :eggs="member.soul_eggs" />
                                ({{ member.eggs_of_prophecy }})
                            </td>
                            <td>{{ member.drones }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3>Settings</h3>
            <div>
                <form>
                    
                </form>
            </div>
        </template>
    </layout>
</template>

<script>
    import Layout from './Layout'
    import EggFormater from '../Components/EggFormater'

    export default {
        components: {
            Layout, EggFormater,
        },
        props: {
            guild: Object,
            guildModel: Object,
            currentContracts: Array,
        }
    }
</script>
