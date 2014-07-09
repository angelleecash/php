<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
<link rel="stylesheet" href="/Public/common.css">
<script src="/Public/echarts-plain.js">
</script>
<script src="/Public/our.js"></script>
<div id="missions">
    <ul></ul>
</div>

<div id="chart"></div>

<style type="text/css">
    .dynamicDiv {
        width: 800px;
        height: 500px;
        border: solid 1px #c0c0c0;
        background-color: #e1e1e1;
        font-size: 11px;
        font-family: verdana;
        color: #000;
        padding: 5px;
    }

    #missions {
        position: absolute;
        left: 0;
        top: 50px;
        bottom: 0;
        z-index: 100;
        width: 300px;
        border-right: 1px solid gainsboro;
    }

    #missions ul {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 10;
        overflow-y: scroll;
    }

    #missions ul li {
        position: relative;
        padding: 10px;
        border-bottom: 1px solid gainsboro;
    }

    #missions ul li:last-child {
        border-bottom-width: 0;
    }

    #missions ul li::after {
        content: "";
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: transparent;
    }

    #missions ul li:hover {
        background: #F3F3F3;
    }

    #missions ul li.active {
        background: #E0E0E0;
    }

    #missions ul li.active::after {
        background: #0072C6;
    }

    #missions ul li strong {
        display: block;
    }

    #missions ul li em {
        display: block;
        color: #247FC2;
        font-size: 12px;
    }

    #missions::after {
        content: "";
        display: none;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: 20;
        background: rgba(255, 255, 255, .75);
    }

    #chart {
        position: absolute;
        left: 301px;
        right: 0;
        top: 50px;
        bottom: 51px;
        min-width: 700px;
        border-width: 1px 0;
        overflow-y: scroll;
    }

    #chart table {
        position: relative;
        z-index: 100;
        table-layout: fixed;
        width: 100%;
    }

    #chart th,
    #chart td {
        border-bottom: 1px solid #F3F3F3;
    }

    #chart th {
        width: 20px;
        padding: 5px;
        background: cornsilk;
        color: gray;
        font-weight: normal;
        line-height: 20px;
    }

    #chart td blockquote {
        min-height: 24px;
        margin: 0;
        padding: 10px;
    }

    #chart td blockquote:focus {
        background: #F3F3F3;
        outline: none;
    }

    #chart td img {
        margin: 0 5px 5px;
        vertical-align: middle;
    }

    #chart tr.statistic td {
        padding: 0 10px;
        line-height: 36px;
    }

    #chart tr.statistic td var {
        display: inline-block;
        margin: 5px;
        padding: 0 10px;
        line-height: 26px;
        background: hsla(215, 55%, 40%, .2);
    }

    #chart tr.level {
        display: none;
    }

    #chart tr.options {
        display: none;
    }

    #chart tr.options div {
        position: relative;
        min-height: 45px;
        padding: 0 50px 0 10px;
        border-bottom: 1px dashed #F3F3F3;
    }

    #chart tr.options div:last-of-type {
        display: none;
        border-bottom-width: 0;
    }

    #chart tr.options dfn {
        position: absolute;
        left: 10px;
        top: 50%;
        width: 26px;
        height: 26px;
        margin-top: -15px;
        margin-right: 5px;
        border-radius: 20px;
        border: 2px solid gray;
        overflow: hidden;
        color: gray;
        font-size: 24px;
        line-height: 26px;
        text-align: center;
        vertical-align: middle;
    }

    #chart tr.options blockquote {
        margin-left: 35px;
        padding: 10px 10px 10px 5px;
        vertical-align: middle;
    }

    #chart tr.options button {
        display: none;
        width: 32px;
        height: 32px;
        margin: 0;
        border: 0 none;
        opacity: .5;
    }

    #chart tr.options button:hover {
        opacity: 1;
    }

    #chart tr.options button.remove {
        position: absolute;
        right: 10px;
        top: 50%;
        margin-top: -16px;
    }

    #chart tr.options button.add {
        position: absolute;
        right: 10px;
        top: 50%;
        margin-top: -16px;
    }

    #chart .options tr.options {
        display: table-row;
    }

    /* TODO */
    #chart tr.solution {
        display: none;
    }

    /* 使用横向排版 */
    #chart {
        background: none;
    }

    #chart th {
        width: auto;
        padding-left: 10px;
        background: aliceblue;
        text-align: left;
    }

    #chart th::after {
        content: "：";
    }

    #chart table,
    #chart tbody,
    #chart tr,
    #chart th,
    #chart td {
        display: block;
    }

    #chart td {
        min-height: 36px;
    }

    #chart .options tr.options {
        display: block;
    }
</style>

<script type="text/javascript" language="javascript">
    var createdCharts = [];

    function createDiv(id) {
        var divTag = document.createElement("div");

        divTag.id = id;

//        divTag.setAttribute("align", "center");

        divTag.style.margin = "0px auto";

        divTag.className = "dynamicDiv";

//        divTag.innerHTML = "This HTML Div tag created "
//                + "using Javascript DOM dynamically.";

//        document.body.appendChild(divTag);

        return divTag;
    }
</script>


<script type="text/javascript">
var $missions = $('#missions');
var $missionList = $missions.find('ul');

var getMissionRequest = new Request('/index.php/Home/Mission/all', {
//        mode: 'jsonp',
    maxTime: 5000
}).on('finish', function (e) {
            var data = JSON.parse(e.text);

            data.forEach(function (mission) {
                var $item = $('<li>' + mission.startTime + '</li>');
                $item.mission = mission;
                $missionList.appendChild($item);
            });

//                setTimeout(function() {
//                    $missions.fire('update');
//                }, 0);
        });

var $activeItem = null;
$missionList.on('click:relay(li)', function () {
    if (this !== $activeItem) {
        if ($activeItem) {
            $activeItem.removeClass('active');
        }
        $activeItem = this.addClass('active');

        var $chart = $("#chart");

//            $chart.deleteContents();

        var getMissionDetailRequest = new Request('/index.php/Home/Mission/detail?missionId=' + $activeItem.mission.id, {
//        mode: 'jsonp',
            maxTime: 5000
        }).on('finish', function (e) {

                    createdCharts.forEach(function (chart) {
                        chart.dispose();
                    });

                    createdCharts = [];

                    while ($chart.hasChildNodes()) {
                        $chart.removeChild($chart.lastChild);
                    }
                    var data = JSON.parse(e.text);

                    var studentNames = data.studentNames;
                    var knowledgeNames = data.knowledges;
                    var knowledgeQuestionsCount = data.knowledgeQuestionCount;
                    var knowledgeAccurateRate = data.accuracy;
                    var knowledgeTimeData = data.knowledgeTimeData;

                    alert(studentNames);
                    alert(knowledgeNames);
                    alert(knowledgeQuestionsCount);
                    alert(knowledgeAccurateRate);
                    alert(knowledgeTimeData);

                    var div = createDiv("main" + 0);
                    $chart.appendChild(div);

                    var myChart = echarts.init(div);

                    createdCharts.push(myChart);

                    var option = {
                        title: {
//                                text: knowledgeNames[i],
//                                subtext: "共" + knowledgeQuestionsCount[i] + "题"
                        },
                        animation: false,
                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {
                            data: []
                        },
                        toolbox: {
                            show: true,
                            feature: {
//                                    mark: {show: true},
                                dataView: {show: true, readOnly: false},
                                magicType: {show: true, type: ['line', 'bar']},
//                                    restore: {show: true},
//                                    saveAsImage: {show: true}
                            }
                        },
                        calculable: true,
                        xAxis: [
                            {
                                type: 'category',
                                data: knowledgeNames
                            }
//                                ,
//                                {
//                                    type: 'category',
//                                    data: knowledgeNames
//                                }
                        ],
                        yAxis: [
                            {
                                name: '正确率',
                                type: 'value',
                                splitArea: {show: true},
                                min: 0,
                                max: 1.0,
                                precision: 1
//                                    ,
//                splitNumber: 10,
//                                    axisLabel: {
//                                        show: true,
//                                        formatter: function (value) {
//                                            return value * 100 + "%";
//                                        }
//                                    }
                            },
                            {
                                name: "学习时间(分钟)",
                                type: 'value',
                                splitArea: {show: true},
                                position: "right",
                                precision: 0

//
//                                    axisLabel: {
//                                        show: true,
//                                        formatter: function (value) {
//                                            return value * 100 + "%";
//                                        }
//                                    }
                            }
                        ],
                        series: [

                        ]
                    };

                    var averageCorrectRate = [];
                    knowledgeAccurateRate.forEach(function (array) {
                        var sum = 0;
                        array.forEach(function (correctRate) {
                            sum += correctRate;
                        });

                        averageCorrectRate.push(sum / studentNames.length);
                    });

                    var averageTimeExpense = [];
                    knowledgeTimeData.forEach(function (array) {
                        var sum = 0.0;
                        array.forEach(function (timeExpense) {
                            var timeExpenseValue = parseFloat(timeExpense);
                            sum += timeExpenseValue;
                        });


                        averageTimeExpense.push(sum / studentNames.length / 1000 / 60);
                    });


                    var legend = ["平均正确率", "学习时间(分钟)"];

                    option.legend.data = legend;

                    option.series.push({
                        name: legend[0],
//                                        type : 'line',
                        type: 'line',
                        data: averageCorrectRate,
//                        markPoint: {
//                            data: [
//                                {type: 'max', name: '最大值'},
//                                {type: 'min', name: '最小值'}
//                            ]
//                        },
                        markLine: {

                            data: [
//                        {type : 'average', name: '平均值'}
//                                {type: 'max', name: '最大值'},    // 最大值水平线或垂直线
//                                {type: 'min', name: '最小值'},    // 最小值水平线或垂直线
                                {type: 'average', name: '所有知识点平均正确率'}
                            ]
                        }
                    });

                    option.series.push({
                        name: legend[1],
//                                        type : 'line',
//                            xAxisIndex: 1,
                        yAxisIndex: 1,
                        type: 'line',
                        data: averageTimeExpense,
//                        markPoint: {
////                            symbol: "triangle",
//                            symbolSize: 0.01,
//                            data: [
//                                {type: 'max', name: '最大值'},
//                                {type: 'min', name: '最小值'}
//                            ]
//                        },
                        markLine: {

                            data: [
//                        {type : 'average', name: '平均值'}
//                                {type: 'max', name: '最大值'},    // 最大值水平线或垂直线
//                                {type: 'min', name: '最小值'},    // 最小值水平线或垂直线
                                {type: 'average', name: '所有知识点平均学习时间'}
                            ]
                        }
                    });

//                        for(var i=0; i < data.knowledgeCount; i++) {
//                            option.legend.data.push(knowledgeNames[i]);
//                            option.series.push({
//                                name: knowledgeNames[i],
////                                        type : 'line',
//                                type: 'line',
//                                data: knowledgeAccurateRate[i],
////                                        markPoint : {
////                                            data : [
////                                                {type : 'max', name: '最大值'},
////                                                {type : 'min', name: '最小值'}
////                                            ]
////                                        },
//                                markLine: {
//
//                                    data: [
////                        {type : 'average', name: '平均值'}
////                        {type : 'max', name: '最大值'},    // 最大值水平线或垂直线
////                        {type : 'min', name: '最小值'},    // 最小值水平线或垂直线
//                                        {type: 'average', name: '平均值'}
//                                    ]
//                                }
//                            });
//                        }

                    myChart.setOption(option);

//                        for (var i = 0; i < data.knowledgeCount; i++) {
//                            var div = createDiv("main" + i);
//                            $chart.appendChild(div);
//
//                            var myChart = echarts.init(div);
//
//                            createdCharts.push(myChart);
//
//                            var option = {
//                                title: {
//                                    text: knowledgeNames[i],
//                                    subtext: "共" + knowledgeQuestionsCount[i] + "题"
//                                },
//                                animation: false,
//                                tooltip: {
//                                    trigger: 'axis'
//                                },
//                                legend: {
//                                    data: ['正确率']
//                                },
//                                toolbox: {
//                                    show: true,
//                                    feature: {
//                                        mark: {show: true},
////                    dataView : {show: true, readOnly: false},
//                                        magicType: {show: true, type: ['line', 'bar']},
//                                        restore: {show: true},
//                                        saveAsImage: {show: true}
//                                    }
//                                },
//                                calculable: true,
//                                xAxis: [
//                                    {
//                                        type: 'category',
//                                        data: studentNames
//                                    }
//                                ],
//                                yAxis: [
//                                    {
//                                        type: 'value',
//                                        splitArea: {show: true},
//                                        min: 0,
//                                        max: 1.0,
//                                        precision: 1,
////                splitNumber: 10,
//                                        axisLabel: {
//                                            show: true,
//                                            formatter: function (value) {
//                                                return value * 100 + "%";
//                                            }
//                                        }
//                                    }
//                                ],
//                                series: [
//                                    {
//                                        name: '正确率',
////                                        type : 'line',
//                                        type: 'line',
//                                        data: knowledgeAccurateRate[i],
////                                        markPoint : {
////                                            data : [
////                                                {type : 'max', name: '最大值'},
////                                                {type : 'min', name: '最小值'}
////                                            ]
////                                        },
//                                        markLine: {
//
//                                            data: [
////                        {type : 'average', name: '平均值'}
////                        {type : 'max', name: '最大值'},    // 最大值水平线或垂直线
////                        {type : 'min', name: '最小值'},    // 最小值水平线或垂直线
//                                                {type: 'average', name: '平均值'}
//                                            ]
//                                        }
//                                    }
//                                ]
//                            };
//
//                            myChart.setOption(option);
//                        }


                });
        getMissionDetailRequest.send();

    }
});

getMissionRequest.send();
</script>
</body>
</html>