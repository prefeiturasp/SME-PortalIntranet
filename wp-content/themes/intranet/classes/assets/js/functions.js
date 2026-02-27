jQuery(document).ready(function ($) {

    let url = window.location.pathname.split('/');
    //if (url[url.length - 1] === 'agenda-secretario.html') {
        $('.calendario-agenda-sec').ionCalendar({
            lang: "pt-br",
            years: "1",
            onClick: function (date) {
                moment.locale('pt-br');
                $('.data_agenda').html(moment(date).format('dddd[,] D [de] MMMM [de] YYYY'));
                let data_pt_br = moment(date).format('DD-MM-YYYY');

                console.log(data_pt_br);
            }
        });
    //}
});