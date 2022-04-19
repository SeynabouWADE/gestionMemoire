/* http://ionden.com/a/plugins/ion.rangeSlider/index.html
    bouton de réinitilisation du range slider : 
    http://ionden.com/a/plugins/ion.rangeSlider/demo_interactions.html
*/
function dateToTS (dateStr) {
    dateStr = new Date(dateStr);
    return dateStr.valueOf()/1000;
}
function tsToDate (ts="", month="long", weekday="", lang = 'default') { //month, weekday must is long or numeric
//new Date(ts) : (ts) : (), ('September 22, 2018 15:00:00'), ('2018-09-22T15:00:00'), (2018, 8, 22), (2018, 8, 22, 15, 0, 0)
    var date = (ts != "") ? new Date(ts * 1000) :  new Date();
    var options = {year: "numeric", month: month, day: "numeric"};
    if(weekday != "")
        options.weekday = weekday;

    return new Intl.DateTimeFormat(lang, options).format(date);
}
function tsToTime (ts="", hour12=false, lang = 'default') { //month in long, numeric
    var date = (ts != "") ? new Date(ts * 1000) :  new Date();
    var options = {hour: "numeric", minute: "numeric", second: "numeric", hour12: hour12};
    return new Intl.DateTimeFormat(lang, options).format(date);
}
function tsToDateTime (ts="", month="long", hour12=false, weekday="", lang = 'default') { //month in long, numeric
    var date = (ts != "") ? new Date(ts * 1000) :  new Date() ,
        options = {year: "numeric", month: month, day: "numeric", hour: "numeric", minute: "numeric", second: "numeric", hour12: hour12};
    if(weekday != "")
        options.weekday = weekday;
    return new Intl.DateTimeFormat(lang, options).format(date);
}
function rangeSliderDateTime(minOrArray, max, from="", to="", grid=false, skin="round", type="single", from_min = "", from_max = "", hide_min_max=false, hide_from_to=false, block=false, prefix="", postfix="", min_prefix="", max_postfix="", force_edges=false, values_separator= " — ", decorate_both=true, typeDate = true, prettify="", prettify_enabled=true, prettify_separator=""){ //prettify un fonction js prédéfinie pour affichage
//TODO 1 revoir les quand il s'agit de tableau de dates
    var isDate_ = false;
    var isArray_ = false;
    var min = minOrArray, values;
    if(Array.isArray(minOrArray)){
        values = minOrArray;
        min = 0;
        max = values.lenght - 1;
        isArray_ = true;
    }
    if(typeDate){
        if(isTime(min)){
            isDate_ = true;
            prettify = tsToTime;
            oneDate = "1970-01-01 ";
            min = oneDate + min;
            max = isTime(from) ? max : oneDate + max;
            from = (from != "" && isTime(from)) ? oneDate + from : from;
            if(type == "double")
                to = (to != "" && isTime(to)) ? oneDate + to : to;
            from_min = (from_min != "" && isTime(from_min)) ? oneDate + from_min : from_min;
            from_max = (from_max != "" && isTime(from_max)) ? oneDate + from_max : from_max;
        }
        else if(isDateTime(min)){
            isDate_ = true;
            prettify = tsToDateTime;
        }else if(isDate(min)){
            isDate_ = true;
            prettify = tsToDate;
        }
        if(isDate_){
            min = dateToTS(min);
            max = dateToTS(max);
            from = (from == "") ? min : dateToTS(from);
            from_min = (from_min == "") ? min : dateToTS(from_min);
            from_max = (from_max == "") ? max : dateToTS(from_max);
            if(type == "double"){
                to = (to == "") ? max : dateToTS(to);
            }
        }
    }
    
    from = (from == "") ? min : from;
    from_min = (from_min == "") ? min : from_min;
    from_max = (from_max == "") ? max : from_max;
    if(type == "double"){
        to = (to == "") ? max : to;
    }
    var data = {
        skin: skin, //big, flat, modern, sharp, round, square
        type: type, //double, single (single par défaut)
        
        min: min,
        max: max,
        from: from,
        to: to,
        from_min: from_min,
        from_max: from_max,

        grid: grid,            // show/hide grid
        force_edges: force_edges,    // force UI in the box
        hide_min_max: hide_min_max,   // show/hide MIN and MAX labels
        hide_from_to: hide_from_to,   // show/hide FROM and TO labels
        block: block,           // block instance from changing
        
        prettify: prettify, // il suffi que je définisse ma fonction my_prettify

        min_prefix: min_prefix, // "-" pour dire et inférieur
        max_postfix: max_postfix, // "+" pour dire et supérieur
        prefix: prefix,      // postfix: " €/ ₽",

        decorate_both: decorate_both, // false, // factoriser les labels quand il collapsé (quand ils sont très proche)
        values_separator: values_separator // " à ", " — ", " → " ...
    };

    if(Array.isArray(minOrArray))
        data.values = values;
    if(! isDate_ && isNumeric(min)){
        if(prettify_enabled)
            data.prettify_enabled = true; //Séparateur de milier
        if(prettify_separator)
            data.prettify_separator = prettify_separator; //Séparateur de milier
    }
    return data;
}
function addResetRangeSliderButton(id, resetText="Reset"){
    var instance = $("#"+id).data("ionRangeSlider");
    $('#reset'+id).html("<input type='button' onclick=\"resetRangeSlider('"+id+"', '"+instance.old_from+"', '"+instance.old_to+"')\" value='"+resetText+"'/>");
}
function resetRangeSlider(id, old_from, old_to){
    var instance = $("#"+id).data("ionRangeSlider");
    console.log(instance.old_from, instance.old_to);
    instance.update({
        from: old_from,
        to: old_to
    });
}