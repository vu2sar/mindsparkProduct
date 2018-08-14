/*! Skillometer version 1.1-SNAPSHOT 2017-07-14
 Skill-o-meter under creativecommons FREE SOFTWARE LICENSE. Check out https://creativecommons.org/ for more information about the contents of this license. author Tejas Rana
 */
"use strict";
angular.module('skillometer', []).factory('skillometer', [function () { //service to manage datatable
                var constructor = function (iConfig) {
                    var skillometer = {
                        configDefault: {
                            name: "skillometer",
                            color: {color1: "#33A86A",
                                color2: "#33A86A",
                                color3: "#33A86A",
                                color4: "#33A86A"},
                            autoColor: false,
                            innerProgress: {showInnerProgress: true,
                                color: ['#FFF', '#33A86A'],
                            },

                            defaultColor: "#808080",
                            innerContent: {
                                showInner: false,
                                innerHeading: "THIS WEEK",
                                innerContentLine1: "Your progress in",
                                innerContentLine2: "Mindspark",
                                onCompleterAll: "WELL DONE!"
                            }
                        },
                        setData: function (data) {
                            if (data.length > 0) {
                                this.setData.block1 = data[0];
                                this.setData.block2 = data[1];
                                this.setData.block3 = data[2];
                                this.setData.block4 = data[3];
                            }
                        },
                        getConfig: function () {
                            return this.config;
                        },
                        setConfig: function (config) {
                            var settings = $.extend(true, {}, this.configDefault, config);
                            this.config = angular.copy(settings);

                        }
                    };
                    skillometer.setConfig(iConfig);
                    return skillometer;
                };
                return constructor;
            }]);
angular.fillSkillOMeter = function (element, value, noFill) {
    angular.resetFillSkillOMeter(element, 0);
    if (typeof noFill !== 'undefined') {
        var durations = 1;
    } else {
        var durations = 1000;
    }
    $('#' + element).animate({offset: value + '%'}, {duration: durations, step: function (now, fx) {
            if (fx.prop === 'offset') {
                $(this).children('stop').attr('offset', Math.round(now * 100) / 100 + '%');
                $(this).children('stop').last().attr('offset', (Math.round(now * 100) / 100) + 0.4 + '%');
            }
        }});
    
    

};
angular.resetFillSkillOMeter = function (element, value, noFill) {
    if (typeof noFill !== 'undefined') {
        var durations = 1;
    } else {
        var durations = 1;
    }
    $('#' + element).animate({offset: value + '%'}, {duration: durations, step: function (now, fx) {
            if (fx.prop === 'offset') {
                $(this).children('stop').attr('offset', Math.round(now * 100) / 100 + '%');
                $(this).children('stop').last().attr('offset', (Math.round(now * 100) / 100) + 0.4 + '%');
            }
        }});

};
angular.hideAllLegends = function () {
    $("#outstanding").hide();
    $("#yourprogressin").hide();
    $("#mindsparkProgress").hide();
    $("#thisweek").hide();
    $("#wellDone").hide();
};
angular.innerProgress = function (reading_val, listening_val, grammar_val, vocabulary_val, colors) {
    var w = 160, h = 160;
    var outerRadius = (w / 2) - 10;
    var innerRadius = 55;
//    var reading_val = 100;
//    var listening_val = 100;
//    var grammar_val = 100;
//    var vocabulary_val = 100;
    var reading_arc = d3.arc().innerRadius(innerRadius).outerRadius(outerRadius).startAngle(0).endAngle(Math.PI / 2.15);
    var listening_arc = d3.arc().innerRadius(innerRadius).outerRadius(outerRadius).startAngle(0).endAngle(Math.PI / 2.15);
    var grammar_arc = d3.arc().innerRadius(innerRadius).outerRadius(outerRadius).startAngle(0).endAngle(Math.PI / 2.15);
    var vocabulary_arc = d3.arc().innerRadius(innerRadius).outerRadius(outerRadius).startAngle(0).endAngle(Math.PI / 2.15);
    var arcLine = d3.arc().innerRadius(innerRadius).outerRadius(outerRadius).startAngle(0);

    $("#inner_progress").html('');
    $("#inner_progress").empty();//clearing div so that each time it load new inner progress bar.
    var svg = d3.select("#inner_progress").append("svg").attr("width", w).attr("height", h).attr("class", 'shadow').attr("y", "95.5").attr("x", "97").append('g').attr("transform", 'translate(' + w / 2 + ',' + h / 2 + ')');
    svg.append('path').attr("d", reading_arc).attr("transform", 'rotate(-90)').attr('stroke-width', "0").attr('stroke', "#666666").style('fill', colors[0]);
    var reading_pathForeground = svg.append('path').datum({endAngle: 0}).attr('d', arcLine).attr("transform", 'rotate(-90)').style("fill", colors[1]);
    svg.append('path').attr("d", listening_arc).attr("transform", 'rotate(0)').attr('stroke-width', "0").attr("stroke", "#666666").style("fill", colors[0]);
    var listening_pathForeground = svg.append('path').datum({endAngle: 0}).attr("d", arcLine).attr("transform", 'rotate(0)').style("fill", colors[1]);
    svg.append('path').attr("d", grammar_arc).attr("transform", 'rotate(+90)').attr('stroke-width', "0").attr("stroke", "#666666").style("fill", colors[0]);
    var grammar_pathForeground = svg.append('path').datum({endAngle: 0}).attr("d", arcLine).attr("transform", 'rotate(+90)').style("fill", colors[1]);
    svg.append('path').attr("d", vocabulary_arc).attr("transform", 'rotate(180)').attr('stroke-width', "0").attr("stroke", "#666666").style("fill", colors[0]);
    var vocabulary_pathForeground = svg.append('path').datum({endAngle: 0}).attr("d", arcLine).attr("transform", 'rotate(180)').style("fill", colors[1]);
    var oldValue = 0;
    var arcTween = function (transition, newValue, oldValue) {
        transition.attrTween("d", function (d) {
            var interpolate = d3.interpolate(d.endAngle, ((Math.PI / 2.15)) * (newValue / 100));
            var interpolateCount = d3.interpolate(oldValue, newValue);
            return function (t) {
                d.endAngle = interpolate(t);
                //middleCount.text(Math.floor(interpolateCount(t))+'%');
                return arcLine(d);
            };
        });
    };
    reading_pathForeground.transition().duration(1000).ease(d3.easePolyIn).call(arcTween, reading_val, oldValue);
    listening_pathForeground.transition().duration(1000).ease(d3.easePolyIn).call(arcTween, listening_val, oldValue);
    grammar_pathForeground.transition().duration(1000).ease(d3.easePolyIn).call(arcTween, grammar_val, oldValue);
    vocabulary_pathForeground.transition().duration(1000).ease(d3.easePolyIn).call(arcTween, vocabulary_val, oldValue);
};
//skill o meter svg renderer
angular.module('skillometer').directive('skillometer', ['$templateCache', function ($templateCache) {
                return {
                    restrict: 'A',
                    scope: false,
                    transclude: true,
                    templateUrl: 'skillometer.html',
                    link: function (scope) {
                        scope.setSkillmeter = function() {
                        scope.skillometer.defaultColor = scope.skillometer.config.defaultColor;

                        scope.skillometer.color1 = scope.skillometer.config.color.color1;
                       
                        scope.skillometer.color2 = scope.skillometer.config.color.color2;

                        scope.skillometer.color3 = scope.skillometer.config.color.color3;

                        scope.skillometer.color4 = scope.skillometer.config.color.color4;

                        var block1 = (scope.skillometer.setData.block1.value / scope.skillometer.setData.block1.total) * 100;
                        var block2 = (scope.skillometer.setData.block2.value / scope.skillometer.setData.block2.total) * 100;
                        var block3 = (scope.skillometer.setData.block3.value / scope.skillometer.setData.block3.total) * 100;
                        var block4 = (scope.skillometer.setData.block4.value / scope.skillometer.setData.block4.total) * 100;


                        if (scope.skillometer.setData.block1.value > scope.skillometer.setData.block1.total) {
                            scope.reading_text = scope.skillometer.setData.block1.value + ' passages';
                            scope.skillometer.setData.block1.total = scope.skillometer.setData.block1.value;
                            block1 = 101;
                        } else {
                            scope.reading_text = scope.skillometer.setData.block1.value + ' of ' + scope.skillometer.setData.block1.total + ' passages';
                        }

                        if (scope.skillometer.setData.block2.value > scope.skillometer.setData.block2.total) {
                            scope.listening_text = scope.skillometer.setData.block2.value + ' audio clips';
                            scope.skillometer.setData.block2.total = scope.skillometer.setData.block2.value;
                            block2 = 101;
                        } else {
                            scope.listening_text = scope.skillometer.setData.block2.value + ' of ' + scope.skillometer.setData.block2.total + ' audio clips';
                        }

                        if (scope.skillometer.setData.block3.value > scope.skillometer.setData.block3.total) {
                            scope.grammar_text = scope.skillometer.setData.block3.value + ' questions';
                            scope.skillometer.setData.block3.total = scope.skillometer.setData.block3.value;
                            block3 = 101;
                        } else {
                            scope.grammar_text = scope.skillometer.setData.block3.value + ' of ' + scope.skillometer.setData.block3.total + ' questions';
                        }

                        if (scope.skillometer.setData.block4.value > scope.skillometer.setData.block4.total) {
                            scope.vocabulary_text = scope.skillometer.setData.block4.value + ' questions';
                            scope.skillometer.setData.block4.total = scope.skillometer.setData.block4.value;
                            block4 = 101;
                        } else {
                            scope.vocabulary_text = scope.skillometer.setData.block4.value + ' of ' + scope.skillometer.setData.block4.total + ' questions';
                        }


                        if (block1 == 101 && block2 == 101 && block3 == 101 && block4 == 101) {
                            angular.hideAllLegends();
                            $("#welldone-right").show();
                            $("#outstanding").show();

                        } else if (block1 >= 100 && block2 >= 100 && block3 >= 100 && block4 >= 100) {
                            angular.hideAllLegends();
                            $("#wellDone").show();
                            $("#welldone-right").show();
                        }


                        if (scope.skillometer.config.autoColor === true) {
                            angular.fillSkillOMeter('reading_percentage', block1);
                            angular.fillSkillOMeter('listening_percentage', block2);
                            angular.fillSkillOMeter('grammar_percentage', block3);
                            angular.fillSkillOMeter('vocabulary_percentage', block4);
                        } else {
                            angular.fillSkillOMeter('reading_percentage', block1, true);
                            angular.fillSkillOMeter('listening_percentage', block2, true);
                            angular.fillSkillOMeter('grammar_percentage', block3, true);
                            angular.fillSkillOMeter('vocabulary_percentage', block4, true);
                        }
                        //showing inner progress
                        angular.innerProgress(scope.skillometer.setData.block1.innerProgress, scope.skillometer.setData.block2.innerProgress, scope.skillometer.setData.block3.innerProgress, scope.skillometer.setData.block4.innerProgress, scope.skillometer.config.innerProgress.color);
                        scope.currentLocation=location.href;
                    };
                    }
                };
            }]);
;"use strict";
angular.module('skillometer').run(['$templateCache', function ($templateCache) {
        $templateCache.put('skillometer.html',
                '<svg width="358" height="354" xmlns="http://www.w3.org/2000/svg" id="skillometerSvg">'
                + '<defs>'
                + '<linearGradient id="reading_percentage">'
                + '<stop stop-color="{{skillometer.color1}}" offset="0%"/>'
                + '<stop stop-color="#808080" offset="0%"/>'
                + '</linearGradient>'
                + '<linearGradient id="listening_percentage">'
                + '<stop stop-color="{{skillometer.color2}}" offset="0%"/>'
                + '<stop stop-color="#808080" offset="0%"/>'
                + '</linearGradient>'
                + '<linearGradient id="grammar_percentage">'
                + '<stop stop-color="{{skillometer.color3}}" offset="0%"/>'
                + '<stop stop-color="#808080" offset="0%"/>'
                + '</linearGradient>'
                + '<linearGradient id="vocabulary_percentage" y2="0" y1="1" x2="1" x1="1">'
                + '<stop stop-color="{{skillometer.color4}}" offset="0%"/>'
                + '<stop stop-color="#808080" offset="0%"/>'
                + '</linearGradient>'
                + '</defs>'
                + '<g>'
                + '<rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/>'
                + '</g>'
                + '<ellipse stroke="#000" fill="#FFF" stroke-width="0" cx="177.24602" cy="175" id="svg_56" rx="55.07751" ry="55.21569"/>'
                + '<g>'
                + '<g id="svg_1">'
                + '<g id="svg_3">'
                + '<g id="svg_69" transform="rotate(-2 100.79484558105547,87.729888916016) ">'
                + '<path fill="url({{currentLocation}}#reading_percentage)" d="m99.52147,169.43393c4.49896,-36.84105 35.65283,-65.44815 73.59754,-66.12718l26.48474,-48.98001l-26.48474,-48.3009c-91.50879,0.67909 -166.0398,72.49375 -171.13308,162.81381l47.96135,-26.31498l49.57419,26.90926z" id="svg_68"/>'
                + '</g>'
                + '<g transform="rotate(-1 89.83345794677984,250.58300781250045) " id="svg_79">'
                + '<path fill="url({{currentLocation}}#vocabulary_percentage)" d="m172.21873,250.84844c-37.18731,-3.88391 -66.54956,-34.76065 -67.90068,-72.96862l-49.8008,-25.81542l-48.18529,27.52577c2.29281,92.1604 75.93851,165.96925 167.00298,169.51146l-27.349,-47.84627l26.23278,-50.40692l0.00001,0z" id="svg_78"/>'
                + '</g>'
                + '<g id="svg_77" transform="rotate(87.58100128173828 263.8941650390625,98.87857055664061) ">'
                + '<path fill="url({{currentLocation}}#listening_percentage)" d="m262.62642,180.20651c4.47821,-36.67135 35.48907,-65.14673 73.25909,-65.82269l26.36285,-48.75458l-26.36285,-48.0786c-91.0878,0.67596 -165.27576,72.1602 -170.34552,162.06432l47.74039,-26.19373l49.34604,26.78528z" id="svg_76"/>'
                + '</g>'
                + '<g id="svg_81" transform="rotate(177.6 254.1021270751953,261.83294677734375) ">'
                + '<path fill="url({{currentLocation}}#grammar_percentage)" d="m252.81694,344.28845c4.54031,-37.17994 35.98074,-66.05014 74.2745,-66.73545l26.7284,-49.43051l-26.7284,-48.74505c-92.35049,0.6853 -167.56697,73.16049 -172.70704,164.31124l48.40229,-26.55706l50.03025,27.15683z" id="svg_80"/>'
                + '</g>'
                + '<g id="svg_23">'
                + ' <g id="svg_5" transform="matrix(0.740744,0,0,0.740744,110.072,24.1103) ">'
                + ' <circle fill="#5EC6D3" cx="-77.7985" cy="226.70883" r="29.5" id="svg_6"/>'
                + '<g id="svg_7">'
                + '<g id="svg_8">'
                + '<g id="svg_9">'
                + '<polygon fill="#FAAF3D" points="-95.29849624633789,233.90805625915527 -86.99849319458008,224.60814476013184 -66.69849395751953,224.70815086364746 -59.19849395751953,233.1080379486084 " id="svg_10"/>'
                + '<path fill="#102F41" d="m-60.3985,244.0088l0,-9.79999l1.20001,0l0,-1l-34,0c-1.79998,0 -3.29998,1.79999 -3.29998,3.89999l0,4.10001c0,2.20001 1.5,3.89999 3.29998,3.89999l34,0l0,-1l-1.20001,0l0,-0.1z" id="svg_11"/>'
                + '<path fill="#002031" d="m-61.09851,244.0088l0,-9.79999l-30.89996,0c-1.5,0 -2.70002,1.5 -2.70002,3.20001l0,3.29999c0,1.79999 1.20002,3.20001 2.70002,3.20001l30.89996,0.09998z" id="svg_12"/>'
                + '<path fill="#F7F1E6" d="m-91.29849,234.20881c-1.5,0 -2.69998,1.5 -2.69998,3.20001l0,3.29999c0,1.79999 1.19998,3.20001 2.69998,3.20001l31.19998,0l0,-9.80002l-31.19998,0.10001z" id="svg_13"/>'
                + '</g>'
                + '<g id="svg_14">'
                + '<polygon fill="#FAAF3D" points="-92.09852981567383,223.00815391540527 -85.39851379394531,215.40826988220215 -68.79853057861328,215.5082302093506 -62.69849395751953,222.30817222595215 " id="svg_15"/>'
                + '<path fill="#058486" d="m-63.69849,231.20881l0,-8l1,0l0,-0.9l-27.70001,0c-1.5,0 -2.70001,1.4 -2.70001,3.19999l0,3.30001c0,1.79999 1.20001,3.19999 2.70001,3.19999l27.70001,0l0,-0.79999l-1,0z" id="svg_16"/>'
                + '<path fill="#017071" d="m-64.19849,231.20881l0,-8l-25.20001,0c-1.20001,0 -2.20001,1.20001 -2.20001,2.70001l0,2.69998c0,1.5 1,2.60001 2.20001,2.60001l25.20001,0z" id="svg_17"/>'
                + '<path fill="#F7F1E6" d="m-88.8985,223.20881c-1.20001,0 -2.20001,1.20001 -2.20001,2.70001l0,2.69998c0,1.5 1,2.60001 2.20001,2.60001l25.5,0l0,-8l-25.5,0z" id="svg_18"/>'
                + '</g>'
                + '</g>'
                + '<g id="svg_19">'
                + '<path fill="#102F41" d="m-78.49851,219.80881c0,0 -0.1,0 0,0c-0.1,0 0,0 0,0l0,0.29999l0.50001,-0.19998l0.19998,0.89999l0.30002,-0.80002l0.5,0.5l0,-0.5l0.69998,0.20002l-0.19998,-0.5c-0.69999,-0.5 -1.4,-0.10001 -2,0.1l-0.00001,0z" id="svg_20"/>'
                + '<path fill="#F05C42" d="m-72.29852,207.0088c-2,-0.29999 -3.69999,0.5 -4.5,1c0,0 0,0 0.10003,0c0,0 0,0 -0.10003,0c-0.09998,0 -0.09998,0.1 -0.19999,0.1c0,0 0,0 0,-0.1c-0.19998,0.1 -0.30001,0.30001 -0.30001,0.5c-0.39997,0.20001 -0.39997,-0.29999 -0.5,-0.60001c0,0 0,0.10001 0,0.10001c-0.69999,-0.5 -2.09998,-1.19999 -4.19999,-1.10001c-2.79998,0.10001 -4.69998,2.20001 -4.6,5.30002c0.10004,4.20001 3.80002,7.79999 6.5,7.79999c0.6,0 1.1,-0.09998 1.6,-0.19999c0,0 0,0 0,0c0,0 -0.1,0 -0.1,0c0,0 0.1,0 0.1,0c0.10001,-0.1 0.29999,-0.20001 0.4,-0.20001c-0.20001,0 -0.29999,0.10001 -0.4,0.20001c0.69999,-0.20001 1.30002,-0.6 2,0l0,0c0.4,0.10001 1.10001,0.29999 1.9,0.29999c2.6,0 6.40002,-2.79999 6.5,-7.1c0,-3.10001 -1.29999,-5.5 -4.20001,-6z" id="svg_21"/>'
                + '<path fill="#102F41" d="m-77.29852,208.5088c0,-0.19999 0.10003,-0.4 0.30001,-0.5c0,-0.60001 0.10001,-1.29999 0.10001,-1.69999c0,-0.6 1,-0.80001 1.5,-1.30005c0.29999,-0.29998 0,-1 0,-1s-2.5,1.20005 -2.40002,2.20005c0,0.5 0,1.1 0,1.69998c0.10003,0.30002 0.10003,0.80002 0.5,0.60001z" id="svg_22"/>'
                + '</g>'
                + '</g>'
                + '</g>'
                + '</g>'
                + '<g id="svg_36">'
                + '<g id="svg_28" transform="matrix(0.721398,0,0,0.721398,227.118,66.346) ">'
                + '<circle fill="#33A86A" cx="-48.23355" cy="320.23541" r="25.99997" id="svg_29"/>'
                + '</g>'
                + '<g id="svg_30" transform="matrix(0.721398,0,0,0.721398,227.118,66.346) ">'
                + '<path fill="#FFFFFF" d="m-52.3335,306.73542l0,-0.70002l0.70001,0l1,0l-0.89996,-2.89996l-6.90003,0l-6.5,21.49994l5.79999,0l1.09998,-4.59998l6.00006,0l1.09997,4.59998l6,0l-3.70001,-11.99994l-0.59997,0l-0.70002,0l0,-0.70001l0,-1.79999l-1.70001,0l-0.70001,0l0,-0.70001l0,-2.70001l0,0zm-4.79999,8.90002l0.40002,-1.5c0.5,-2 1.09998,-4.60004 1.5,-6.79999l0.09998,0c0.5,2.09998 1.10004,4.79999 1.60004,6.79999l0.39996,1.5l-4,0z" id="svg_31"/>'
                + '<polygon fill="#FFFFFF" points="-44.13348197937012,306.7352066040039 -46.53344917297363,306.7352066040039 -46.53344917297363,304.33524322509766 -49.3335018157959,304.33524322509766 -49.3335018157959,306.7352066040039 -50.533456802368164,306.7352066040039 -51.733469009399414,306.7352066040039 -51.733469009399414,309.53519439697266 -49.63349151611328,309.53519439697266 -49.3335018157959,309.53519439697266 -49.3335018157959,310.53519439697266 -49.3335018157959,311.93521881103516 -48.9334774017334,311.93521881103516 -46.53344917297363,311.93521881103516 -46.53344917297363,309.53519439697266 -44.13348197937012,309.53519439697266 " id="svg_32"/>'
                + '</g>'
                + '<g id="svg_33" transform="matrix(0.721398,0,0,0.721398,227.118,66.346) ">'
                + '<path fill="#FFFFFF" d="m-45.73346,331.43543l4.39996,0l0.80005,3.39996l4.39996,0l-3.29999,-10.70001l-1.70001,0l-0.5,0l0,-0.5l0,-2l0,-0.5l0.5,0l0.79999,0l-0.59998,-2.19995l-5.09997,0l-4.80005,15.79999l4.30005,0l0.79998,-3.29999l0.00001,0zm1,-4.40003c0.39996,-1.5 0.79998,-3.40002 1.09997,-5l0.10004,0c0.39996,1.59998 0.79999,3.5 1.19995,5l0.30005,1.09998l-2.90003,0l0.20002,-1.09998z" id="svg_34"/>'
                + '<polygon fill="#FFFFFF" points="-35.43346452713013,321.53519439697266 -40.1334810256958,321.53519439697266 -41.03344440460205,321.53519439697266 -41.03344440460205,323.63516998291016 -39.53344249725342,323.63516998291016 -35.43346452713013,323.63516998291016 " id="svg_35"/>'
                + '</g>'
                + '</g>'
                + '<g id="svg_63">'
                + '<g id="svg_58" transform="matrix(0.645891,0,0,0.645891,336.908,40.4756) ">'
                + '<circle fill="#F8841D" cx="-55.55979" cy="188.73873" r="28.29999" id="svg_59"/>'
                + '<g id="svg_60">'
                + '<path fill="#F1E4C4" d="m-43.15984,190.83878c0,6.89997 -6.29999,12.59998 -14,12.59998c-2.29999,0 -4.5,-0.5 -6.39999,-1.39996c-0.69998,-0.30005 -9.10001,0.29998 -9.69999,-0.30005c-0.6,-0.59998 4.5,-3.79999 4,-4.59998c-1.20001,-1.90002 -1.90002,-4 -1.90002,-6.29999c0,-6.90002 6.30002,-12.60003 14,-12.60003c7.70001,0.10003 14,5.70001 14,12.60003z" id="svg_61"/>'
                + '<path fill="#FFFFFF" d="m-59.55983,180.53879c0,5.39997 4.80002,9.69996 10.80002,9.69996c1.79998,0 3.39999,-0.39997 4.89999,-1.09998c0.5,-0.20001 7,0.29999 7.5,-0.20001c0.5,-0.5 -3.39999,-3 -3.10001,-3.5c0.9,-1.39997 1.5,-3.09998 1.5,-4.89997c0,-5.40002 -4.79998,-9.70001 -10.79998,-9.70001s-10.80002,4.29999 -10.80002,9.70001z" id="svg_62"/>'
                + ' </g>'
                + '</g>'
                + '</g>'
                + '<g id="svg_53">'
                + ' <g transform="matrix(0.698465,0,0,0.698465,425.634,38.194) " id="svg_41">'
                + ' <circle id="svg_42" r="28.99995" cy="20.83102" cx="-370.432292" fill="#F26D64"/>'
                + ' <g id="svg_43">'
                + '  <rect id="svg_44" height="28.2" width="8.3" fill="#F7F1E6" y="9.03102" x="-375.432292"/>'
                + '  <rect id="svg_45" height="25.9" width="10.8" fill="#F7F1E6" transform="matrix(0.9658,-0.2591,0.2591,0.9658,-7.108,11.8421) " y="-91.493365" x="-347.692572"/>'
                + '  <path id="svg_46" d="m-362.132352,13.03099c-0.09997,-0.29999 -0.39996,-0.5 -0.70001,-0.5c-0.29999,0.1 -0.5,0.39999 -0.5,0.70001l6,24.10001c0.10004,0.29998 0.40003,0.5 0.70001,0.5c0.29999,-0.10001 0.5,-0.4 0.40003,-0.70002l-5.90003,-24.1z" fill="#058486"/>'
                + ' <path id="svg_47" d="m-359.132352,12.43098c-0.09997,-0.29999 -0.39996,-0.5 -0.70001,-0.5c-0.29999,0.10001 -0.5,0.40003 -0.39996,0.70001l6,24.10001c0.09997,0.29999 0.39996,0.5 0.70001,0.5c0.29999,-0.10001 0.5,-0.39999 0.39996,-0.70001l-6,-24.10001z" fill="#058486"/>'
                + '  <path id="svg_48" d="m-366.532312,12.63099l0,-5.59999c0,-1.29999 -1,-2.4 -2.30005,-2.4l-4.89996,0c-1.29999,0 -2.29999,1 -2.40003,2.4l0,30.2c0,1.29999 1,2.39999 2.29999,2.39999l4.90003,0c1.29998,0 2.40002,-1.1 2.40002,-2.29998l0,-19.10001l-1,-3.89999c-0.10004,-0.90003 0.29999,-1.60001 1,-1.70002zm-2.80005,23.4l-4,0c-0.5,0 -0.79999,-0.4 -0.79999,-0.79999c0,-0.5 0.40003,-0.79999 0.79999,-0.79999l4,0c0.5,0 0.80005,0.4 0.80005,0.79999c0,0.39999 -0.30005,0.79999 -0.80005,0.79999zm0,-23.60001l-4,0c-0.5,0 -0.79999,-0.39999 -0.79999,-0.79999c0,-0.5 0.40003,-0.79998 0.79999,-0.79998l4,0c0.5,0 0.80005,0.39999 0.80005,0.79998c0,0.4 -0.30005,0.79999 -0.80005,0.79999z" fill="#FAAF3D"/>'
                + '<g id="svg_49">'
                + '<rect id="svg_50" height="28.2" width="8.3" fill="#F7F1E6" y="8.23102" x="-385.232292"/>'
                + '<path id="svg_51" d="m-378.732322,4.131l-4.90002,0c-1.29999,0 -2.39997,1 -2.39997,2.4l0,30.2c0,1.29999 1,2.39999 2.29999,2.39999l4.89996,0c1.30005,0 2.40003,-1.1 2.40003,-2.29998l0,-30.20001c0.09997,-1.5 -1,-2.5 -2.29999,-2.5zm-0.40002,31.39999l-4,0c-0.5,0 -0.79999,-0.4 -0.79999,-0.79999c0,-0.5 0.40002,-0.79999 0.79999,-0.79999l4,0c0.5,0 0.79998,0.4 0.79998,0.79999c0,0.39999 -0.39996,0.79999 -0.79998,0.79999zm0,-23.60001l-4,0c-0.5,0 -0.79999,-0.39999 -0.79999,-0.79999c0,-0.5 0.40002,-0.79998 0.79999,-0.79998l4,0c0.5,0 0.79998,0.39999 0.79998,0.79998c0,0.4 -0.39996,0.79999 -0.79998,0.79999z" fill="#058486"/>'
                + '</g>'
                + '<path id="svg_52" d="m-348.932332,35.53099c-0.09998,-0.59998 -6,-24 -6,-24c-0.09998,-0.69998 -0.79999,-1.19998 -1.59998,-1c-0.70001,0.1 -1.20001,0.80002 -1,1.6l5.5,22.10001l-7.40002,1.70001l-5.59998,-22.30002c-0.10004,-0.70001 -0.80005,-1.20001 -1.60004,-1c0,0 0,0 -0.09997,0c-0.70001,0.20002 -1.10004,0.79999 -1,1.5l1,3.9l5,20.20001c0.09997,0.70001 0.79999,1.10001 1.5,1l0,0l10.20001,-2.29999l0,0c0,0 0,0 0,0c0.89996,-0.20001 1.19995,-0.6 1.09998,-1.40002z" fill="#102F41"/>'
                + '</g>'
                + '</g>'
                + '</g>'
                + '<g display=" none" id="welldone-right"  style="display:none;">'
                + '<g id="svg_86" transform="matrix(0.694819,0,0,0.694819,267.34,170.662) ">'
                + ' <g id="svg_87">'
                + '  <polygon fill="#7AC943" points="-106.8480281829834,-38.974215030670166 -138.14804649353027,-7.6742024421691895 -150.04804039001465,-19.574191570281982 -162.3480281829834,-7.274215221405029 -150.44803428649902,4.625766277313232 -138.14804649353027,16.82577657699585 -125.94803428649902,4.625766277313232 -94.64804649353027,-26.77420663833618 " id="svg_88"/>'
                + '</g>'
                + '</g>'
                + ' </g>'
                + '<g transform="rotate(3 186,179) " id="inner_progress">'
                + '</g>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" x="61.71765" y="256.55146" id="svg_90" font-size="16" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" font-weight="normal" transform="matrix(1.19596,0,0,1.19596,-54.7881,-62.9996) ">Vocabulary</text>'
                + ' <text fill="#ffffff" stroke="#000" stroke-width="0" x="282.99541" y="254.75147" id="svg_91" font-size="16" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" transform="matrix(1.2298199999999997,0,0,1.2298199999999997,-107.735,-75.1669) ">Grammar</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="278.28721" y="111.63221" id="svg_92" font-size="16" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" transform="matrix(1.4391499999999997,0,0,1.4391499999999997,-198.037,-80.4023) ">Listening</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="159" y="96" id="svg_93" font-size="16" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" transform="matrix(1.48828,0,0,1.48828,-188.008,-61.4312) ">Reading</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="49" y="284.90774" id="svg_100" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">attempted</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="46" y="268" id="vocabulary_text" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">{{vocabulary_text}}</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="201.74172" y="277" id="svg_102" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">attempted</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="202" y="259.59451" id="grammar_text" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">{{grammar_text}}</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="222" y="114" id="svg_104" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">heard</text>'
                + '<text y="98" x="222" fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" id="listening_text" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">{{listening_text}}</text>'
                + '<text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="32.73275" y="120" id="svg_106" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">read</text>'
                + ' <text fill="#ffffff" stroke="#000" stroke-width="0" stroke-opacity="null" fill-opacity="null" x="32.73275" y="103" id="reading_text" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve">{{reading_text}}</text>'
                + '<text fill="#546e7a" stroke="#000" stroke-width="0" stroke-opacity="null" x="136.90295" y="202.12619" id="wellDone" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" font-weight="bold"  style="display:none;">WELL DONE!</text>'
                + '<text fill="#546e7a" stroke="#000" stroke-width="0" stroke-opacity="null" x="136.90295" y="202.12619" id="outstanding" font-size="12" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" font-weight="bold"  style="display:none;">Outstanding!</text>'
                + '<text fill="#46626F" stroke="#000" stroke-width="0.4" stroke-opacity="null" x="129.5" y="171.12619" id="thisweek" font-size="16" font-family="Gotham-Book" text-anchor="start" xml:space="preserve"  style="">THIS WEEK</text>'
                + '<text fill="#898989" stroke="#000" stroke-width="0" stroke-opacity="null" x="132" y="191.12619" id="yourprogressin" font-size="11" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" style="">Your progress in</text>'
                + '<text fill="#898989" stroke="#000" stroke-width="0" stroke-opacity="null" x="149" y="205.12619" id="mindsparkProgress" font-size="11" font-family="Gotham-Book" text-anchor="start" xml:space="preserve" style="">Mindspark</text>'
                + '</g>'
                + '</g>'
                + '</g>'
                + '</svg>');
    }]);