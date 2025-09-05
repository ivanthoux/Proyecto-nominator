Handlebars.registerHelper('decimal', function (number) {
    return parseFloat(parseInt(number)).toFixed(2);
});
