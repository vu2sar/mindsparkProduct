/**
 * function description : This function will show the skill o meter to students only..
 * param 1: data is use to transmit data from api to svg.
 * @return  render all recently essay, pending essay and todays active topic.
 * 
 * */
englishInterface.controller('skillOmeterController', function ($scope, $http, skillometer) {
    $scope.getSkillOmeterData = function () {
        $http({
            method: 'POST',
            url: Helpers.constants['CONTROLLER_PATH'] + "home/getskillometer",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            data: $scope.skillData
        }).success(function (data) {
            var data = $.parseJSON(data.result_data);
            var config = {
                "name": "student skill o meter",
                "color": {color1: "#33A86A",
                    color2: "#5ec6d3",
                    color3: "#f26d64",
                    color4: "#f8841d"},
                "autoColor": true,
                innerProgress: {showInnerProgress: true,
                    color: ['transparent', '#33A86A']
                },
                "innerContent": {
                    showInner: true,
                    innerHeading: "THIS WEEK",
                    innerContentLine1: "Your progress in",
                    innerContentLine2: "Mindspark",
                    "onCompleterAll": "WELL DONE!"
                }
            };
            var skillometerData = [{
                    "heading": "Reading",
                    "value": data.readTotalPsgRead,
                    "total": 2,
                    "innerProgress": data.readQuesAcc,
                    "postfix": "passages",
                    "subheading": "read"
                }, {
                    "heading": "Listening",
                    "value": data.listenTotalPsgRead,
                    "total": 2,
                    "innerProgress": data.listenQuesAcc,
                    "postfix": "audio clips",
                    "subheading": "heard"
                }, {
                    "heading": "Grammar",
                    "value": data.grammarTotalQues,
                    "total": 20,
                    "innerProgress": data.grammarQuesAcc,
                    "postfix": "questions",
                    "subheading": "attempted"
                }, {
                    "heading": "Vocabulary",
                    "value": data.vocabTotalQues,
                    "total": 20,
                    "innerProgress": data.vocabQuesAcc,
                    "postfix": "passages",
                    "subheading": "attempted"
                },
            ];
            $scope.skillometer = skillometer(config);
            $scope.skillometer.setData(skillometerData);
            $scope.setSkillmeter();
        });
    };
});
/**
 * function description : This function will fetch data from getskillometer function..
 * @return  call getSkillometerdata function and appear 
 * 
 * */
function showskillometer() {
    angular.element(document.getElementById('skillometer')).scope().getSkillOmeterData();
}