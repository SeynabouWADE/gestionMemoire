<script>
            window.onload = function() {
                var charts = [];
                var toolTip = {
                        shared: true
                    },
                    legend = {
                        cursor: "pointer",
                        itemclick: function(e) {
                            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                                e.dataSeries.visible = false;
                            } else {
                                e.dataSeries.visible = true;
                            }
                            e.chart.render();
                        }
                    };

                var ebc_crachat = [],
                    aspirations_bronchiques = [],
                    bronchage_bronchique_protege = [],
                    lavage_broncho_alveolaire = [],
                    hemoculture = [],
                    lcr = [],
                    liquide_articulaire = [],
                    liquide_pleural = [],
                    urine = [],
                    selles = [],
                    pus_superficiel = [],
                    pus_profond = [],
                    prelevement_vaginal = [],
                    prelevement_uretral = [],
                    prelevements_gastriques = [],
                    catheter = [],
                    sonde = [],
                    homme = [],
                    femme = [],
                    classique = [],
                    galerie_api2 = [],
                    nr = [],
                    vitek2 = [],
                    maldi_tof = [],
                    non_renseigne = [],
                    non = [];

                var prelChartOptions = {
                    animationEnabled: true,
                    theme: "light2", // "light1", "light2", "dark1", "dark2"
                    title: {
                        text: "Prelevement en fonction des services"
                    },
                    toolTip: toolTip,
                    axisY: {
                        valueFormatString: "#0.#%",
                    },
                    legend: legend,
                    data: [{
                            type: "splineArea",
                            showInLegend: "true",
                            name: "ebcOuCrachat",
                            color: "#64b5f6",
                            legendMarkerType: "square",
                            dataPoints: ebc_crachat
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "aspirationsBronchiques",
                            color: "#2196f3",
                            legendMarkerType: "square",
                            dataPoints: aspirations_bronchiques
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "lavageBronchoAlveolaire",
                            color: "#1976d2",
                            legendMarkerType: "square",
                            dataPoints: lavage_broncho_alveolaire
                        },
                        {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "hemoculture",
                            color: "#64b5f6",
                            legendMarkerType: "square",
                            dataPoints: hemoculture
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "lcr",
                            color: "#2196f3",
                            legendMarkerType: "square",
                            dataPoints: lcr
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "liquideArticulaire",
                            color: "#1976d2",
                            legendMarkerType: "square",
                            dataPoints: liquide_articulaire
                        },


                        {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "LiquidePleural",
                            color: "#64b5f6",
                            legendMarkerType: "square",
                            dataPoints: liquide_pleural
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "Selles",
                            color: "#2196f3",
                            legendMarkerType: "square",
                            dataPoints: selles
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "pusProfond",
                            color: "#1976d2",
                            legendMarkerType: "square",
                            dataPoints: pus_profond
                        },
                        {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "prelevementUretral",
                            color: "#64b5f6",
                            legendMarkerType: "square",
                            dataPoints: prelevement_uretral
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "prelevementsGastriques",
                            color: "#2196f3",
                            legendMarkerType: "square",
                            dataPoints: prelevements_gastriques
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "catheter",
                            color: "#1976d2",
                            legendMarkerType: "square",
                            dataPoints: catheter
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "sonde",

                            color: "#1976d2",


                            legendMarkerType: "square",
                            dataPoints: sonde
                        }
                    ]
                };
                var sexChartOptions = {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: "Repartition ds patients dans les services en fonction ds sexe"
                    },
                    axisY: {
                        suffix: "patients"
                    },
                    toolTip: toolTip,
                    legend: legend,
                    data: [{
                        type: "splineArea",
                        showInLegend: "true",
                        name: "Homme",
                        color: "#e57373",



                        legendMarkerType: "square",
                        dataPoints: homme
                    }, {
                        type: "splineArea",
                        showInLegend: "true",
                        name: "Femme",
                        color: "#f44336",



                        legendMarkerType: "square",
                        dataPoints: femme
                    }]
                };
                var nosocChartOptions = {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: "Repartition des Infections nosocomiales en fonction des services"
                    },
                    axisY: {
                        suffix: "patients"
                    },
                    toolTip: toolTip,
                    legend: legend,
                    data: [{
                        type: "splineArea",
                        showInLegend: "true",
                        name: "non renseigne",
                        color: "#81c784",

                        legendMarkerType: "square",
                        dataPoints: non_renseigne
                    }, {
                        type: "splineArea",
                        showInLegend: "true",
                        name: "non",
                        color: "#388e3c",

                        legendMarkerType: "square",
                        dataPoints: non
                    }]
                };
                var methodIdChartOptions = {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: "Repartition des Germes en fonction des profil de resistances"
                    },
                    axisY: {},
                    toolTip: toolTip,
                    legend: legend,
                    data: [{
                            type: "splineArea",
                            showInLegend: "true",
                            name: "classique",
                            color: "#ffb74d",
                            legendMarkerType: "square",
                            dataPoints: classique
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "galerie_API2",
                            color: "#f57c00",
                            legendMarkerType: "square",
                            dataPoints: galerie_api2
                        },
                        {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "nr",
                            color: "#ffb74d",
                            legendMarkerType: "square",
                            dataPoints: nr
                        }, {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "vitek2",
                            color: "#f57c00",
                            legendMarkerType: "square",
                            dataPoints: vitek2
                        },
                        {
                            type: "splineArea",
                            showInLegend: "true",
                            name: "maldi_tof",
                            color: "#ffb74d",
                            legendMarkerType: "square",
                            dataPoints: maldi_tof
                        }
                    ]
                }

                charts.push(new CanvasJS.Chart("chartContainer1", prelChartOptions));
                charts.push(new CanvasJS.Chart("chartContainer2", sexChartOptions));
                charts.push(new CanvasJS.Chart("chartContainer3", nosocChartOptions));
                charts.push(new CanvasJS.Chart("chartContainer4", methodIdChartOptions));

                $.get("donnesCliniques.json", function(data) {
                    data = JSON.parse(data);
                    console.log(data);
                    for (var i = 1; i < data.length; i++) {
                        ebc_crachat.push({
                            x: data[i].service,
                            y: parseInt(data[i].ECBC_ou_crachat)
                        });
                        aspirations_bronchiques.push({
                            x: data[i].service,
                            y: parseInt(data[i].Aspirations_bronchiques)
                        });
                        bronchage_bronchique_protege.push({
                            x: data[i].service,
                            y: parseInt(data[i].Bronchage_bronchique_protege)
                        });
                        lavage_broncho_alveolaire.push({
                            x: data[i].service,
                            y: parseInt(data[i].Lavage_Broncho_alveolaire)
                        });
                        hemoculture.push({
                            x: data[i].service,
                            y: parseInt(data[i].Hemoculture)
                        });
                        lcr.push({
                            x: data[i].service,
                            y: parseInt(data[i].Lcr)
                        });
                        liquide_articulaire.push({
                            x: data[i].service,
                            y: parseInt(data[i].Liquide_articulaire)
                        });
                        liquide_pleural.push({
                            x: data[i].service,
                            y: parseInt(data[i].Liquide_pleural)
                        });
                        urine.push({
                            x: data[i].service,
                            y: parseInt(data[i].Urine)
                        });
                        selles.push({
                            x: data[i].service,
                            y: parseInt(data[i].Selles)
                        });

                        pus_superficiel.push({
                            x: data[i].service,
                            y: parseInt(data[i].Pus_superficiel)
                        });
                        pus_profond.push({
                            x: data[i].service,
                            y: parseInt(data[i].Pus_profond)
                        });
                        prelevement_vaginal.push({
                            x: data[i].service,
                            y: parseInt(data[i].Prelevement_vaginal)
                        });
                        prelevement_uretral.push({
                            x: data[i].service,
                            y: parseInt(data[i].Prelevement_uretral)
                        });


                        prelevements_gastriques.push({
                            x: data[i].service,
                            y: parseInt(data[i].Prelevements_gastriques)
                        });
                        catheter.push({
                            x: data[i].service,
                            y: parseInt(data[i].Catheter)
                        });
                        sonde.push({
                            x: data[i].service,
                            y: parseInt(data[i].Sonde)
                        });
                        homme.push({
                            x: data[i].service,
                            y: parseInt(data[i].homme)
                        });
                        femme.push({
                            x: data[i].service,
                            y: parseInt(data[i].femme)
                        });

                        classique.push({
                            x: data[i].service,
                            y: parseInt(data[i].classique)
                        });
                        galerie_api2.push({
                            x: data[i].service,
                            y: parseInt(data[i].galerie_API2)
                        });
                        nr.push({
                            x: data[i].service,
                            y: parseInt(data[i].nr)
                        });
                        vitek2.push({
                            x: data[i].service,
                            y: parseInt(data[i].vitek2)
                        });
                        maldi_tof.push({
                            x: data[i].service,
                            y: parseInt(data[i].maldi_tof)
                        });
                        non_renseigne.push({
                            x: data[i].service,
                            y: parseInt(data[i].non_renseigne)
                        });
                        non.push({
                            x: data[i].service,
                            y: parseInt(data[i].non)
                        })
                    }
                    console.log(classique);
                    console.log(galerie_api2);
                    console.log(nr);
                    console.log(maldi_tof);
                    console.log(non_renseigne);
                    console.log(non);
                    for (var i = 0; i < charts.length; i++) {
                        charts[i].options.axisX = {
                            labelAngle: 0,
                            crosshair: {
                                enabled: true,
                                snapToDataPoint: true,
                            }
                        }
                    }
                    syncCharts(charts, true, true, true); // syncCharts(charts, syncToolTip, syncCrosshair, syncAxisXRange)
                    for (var i = 0; i < charts.length; i++) {
                        charts[i].render();
                    }
                });

                function syncCharts(charts, syncToolTip, syncCrosshair, syncAxisXRange) {

                    if (!this.onToolTipUpdated) {
                        this.onToolTipUpdated = function(e) {
                            for (var j = 0; j < charts.length; j++) {
                                if (charts[j] != e.chart)
                                    charts[j].toolTip.showAtX(e.entries[0].xValue);
                            }
                        }
                    }
                    if (!this.onToolTipHidden) {
                        this.onToolTipHidden = function(e) {
                            for (var j = 0; j < charts.length; j++) {
                                if (charts[j] != e.chart)
                                    charts[j].toolTip.hide();
                            }
                        }
                    }
                    if (!this.onCrosshairUpdated) {
                        this.onCrosshairUpdated = function(e) {
                            for (var j = 0; j < charts.length; j++) {
                                if (charts[j] != e.chart)
                                    charts[j].axisX[0].crosshair.showAt(e.value);
                            }
                        }
                    }
                    if (!this.onCrosshairHidden) {
                        this.onCrosshairHidden = function(e) {
                            for (var j = 0; j < charts.length; j++) {
                                if (charts[j] != e.chart)
                                    charts[j].axisX[0].crosshair.hide();
                            }
                        }
                    }
                    if (!this.onRangeChanged) {
                        this.onRangeChanged = function(e) {
                            for (var j = 0; j < charts.length; j++) {
                                if (e.trigger === "reset") {
                                    charts[j].options.axisX.viewportMinimum = charts[j].options.axisX.viewportMaximum = null;
                                    charts[j].options.axisY.viewportMinimum = charts[j].options.axisY.viewportMaximum = null;
                                    charts[j].render();
                                } else if (charts[j] !== e.chart) {
                                    charts[j].options.axisX.viewportMinimum = e.axisX[0].viewportMinimum;
                                    charts[j].options.axisX.viewportMaximum = e.axisX[0].viewportMaximum;
                                    charts[j].render();
                                }
                            }
                        }
                    }
                    for (var i = 0; i < charts.length; i++) {
                        if (syncToolTip) {
                            if (!charts[i].options.toolTip)
                                charts[i].options.toolTip = {};
                            charts[i].options.toolTip.updated = this.onToolTipUpdated;
                            charts[i].options.toolTip.hidden = this.onToolTipHidden;
                        }
                        if (syncCrosshair) {
                            if (!charts[i].options.axisX)
                                charts[i].options.axisX = {
                                    crosshair: {
                                        enabled: true
                                    }
                                };
                            charts[i].options.axisX.crosshair.updated = this.onCrosshairUpdated;
                            charts[i].options.axisX.crosshair.hidden = this.onCrosshairHidden;
                        }
                        if (syncAxisXRange) {
                            charts[i].options.zoomEnabled = true;
                            charts[i].options.rangeChanged = this.onRangeChanged;
                        }
                    }
                }
            }
        </script>
        <div class="row">
            <div class="col" id="chartContainer1"></div>
            <div class="col" id="chartContainer2"></div>
        </div>
        <div class="row">
            <div class="col" id="chartContainer3"></div>
            <div class="col" id="chartContainer4"></div>
        </div>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>