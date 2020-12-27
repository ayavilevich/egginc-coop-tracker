<template>
    <layout title="Home">
        <template v-if="$parent.$page.user">
            <div class="row">
                <div class="col-md-6">
                    <h2>Your Contracts</h2>
                    <div class="list-group">
                        <div class="list-group-item" v-for="contract in playerInfo.contracts.contractsList">
                            <h3>{{ contract.contract.name }} - {{ contract.coopIdentifier }}</h3>
                            
                        </div>
                    </div>

                    <h2>Discord Servers With EggBert</h2>

                    <div class="list-group">
                        <a v-for="guild in guilds" class="list-group-item list-group-item-action flex-column" :href="route('guild.index', {'guildId': guild.id})">
                            <h5 class="mb-1">{{ guild.name }}</h5>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 list-group">
                    <h2>Player Info</h2>
                    <div class="list-group-item">
                        <p>
                            Soul Eggs:
                            <EggFormater :eggs="playerInfo.game.soulEggsD" />
                        </p>

                        <p>
                            Golden Eggs:
                            {{ playerInfo.game.eggsOfProphecy }}
                        </p>

                        <p>
                            Rank:
                            {{ user.player_egg_rank }}
                        </p>

                        <p>
                            Earning Bonus:
                            {{ user.player_earning_bonus_formatted }}
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <template v-if="!$parent.$page.user">
            <p>Sign in with Discord to the left to view your guilds and Player Info</p>
        </template>
    </layout>
</template>

<script>
    import Layout from './Layout'
    import EggFormater from '../Components/EggFormater'

    export default {
        components: {
            Layout,
            EggFormater,
        },
        props: {
            guilds: Object,
            playerInfo: Object,
            user: Object,
        }
    }
</script>
