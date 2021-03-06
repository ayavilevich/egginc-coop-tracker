'use strict';

const Discord = require('discord.js');
const client = new Discord.Client();
const axios = require('axios');
const _ = require('lodash');

require('dotenv').config();

client.on('ready', () => {
    console.log('bot is ready');
});

client.on('message', message => {
    let atBotUser = 'eb!';
    message.content = message.content.toLowerCase();

    if (message.author.bot || !message.content.startsWith(atBotUser)) {
        return;
    }

    message.channel.startTyping();

    console.log(message.content);

    let messageDetails = message.toJSON();
    messageDetails.atBotUser = atBotUser;
    messageDetails.channel = message.channel.toJSON();
    messageDetails.channel.guild = message.channel.guild ? message.channel.guild.toJSON() : {};
    messageDetails.author = message.author.toJSON();

    axios.post(process.env.DISCORD_API_URL + '/api/discord-message', messageDetails)
        .then(function (response) {
            if (response.data.message) {
                if (_.isArray(response.data.message)) {
                    _.forEach(response.data.message, function (messageToSend) {
                        message.channel.send(messageToSend)
                    })
                } else {
                    message.channel.send(response.data.message);
                }
            } else {
                message.channel.send('I have nothing to say.');
            }

            message.channel.stopTyping();
        })
        .catch(function (error) {
            console.log(error.toJSON());
            message.channel.send('An error has occurred.');

            message.channel.stopTyping();
        })
    ;
})

client.login(process.env.DISCORD_BOT_TOKEN);
