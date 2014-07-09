<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
    <meta charset="UTF-8">
    <title>Test</title>
</head>
<body>
<!--<?php $__FOR_START_1012349095__=0;$__FOR_END_1012349095__=30;for($i=$__FOR_START_1012349095__;$i < $__FOR_END_1012349095__;$i+=1){ ?>-->
    <!--<div id="main<?php echo ($i); ?>" style="height: 500px;border: 1px solid #ccc; padding: 10px;"></div>-->
<!--<?php } ?>-->
<!--<div id="main" style="height: 500px;border: 1px solid #ccc; padding: 10px;"></div>-->
</body>
<script src="/Public/echarts-plain.js">
</script>
<script type="text/javascript">

    var knowledgeCount = <?php echo ($knowledgeCount); ?>;
    for(var i=0; i < knowledgeCount; i++) {
//        document.write("<div id=\"main\"" + i + "style=\"height: 500px;border: 1px solid #ccc; padding: 10px;\"></div>");
        document.write("<div id=\"main" + i + "\" style=\"height: 500px;border: 1px solid #ccc; padding: 10px;\"></div>");
    }

    var knowledgeNames = new Array(<?php echo ($knowledgeNames); ?>);
    var knowledgeQuestionsCount = new Array(<?php echo ($knowledgeQuestionsCount); ?>);
    var knowledgeAccurateRate = <?php echo ($knowledgeAccurateRate); ?>;


    for (var i=0; i < knowledgeCount; i++)
    {
        var myChart = echarts.init(document.getElementById("main"+i));
        myChart.setOption({
            title: {
                text:knowledgeNames[i],
                subtext:"共"+knowledgeQuestionsCount[i]+"题"
            },
            tooltip : {
                trigger: 'axis'
            },
            legend: {
                data: ['正确率']
            },
            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
//                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            xAxis : [
                {
                    type: 'category',
                    data: [<?php echo ($studentNames); ?>]
                }
            ],
            yAxis : [
                {
                    type : 'value',
                    splitArea : {show : true},
                    min:0,
                    max:1.0,
                    precision: 1,
//                splitNumber: 10,
                    axisLabel:{
                        show : true,
                        formatter: function (value){return value*100 + "%";}
                    }
                }
            ],
            series : [
                {
                    name : '正确率',
                    type : 'bar',
                    data : knowledgeAccurateRate[i],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {

                        data : [
//                        {type : 'average', name: '平均值'}
//                        {type : 'max', name: '最大值'},    // 最大值水平线或垂直线
//                        {type : 'min', name: '最小值'},    // 最小值水平线或垂直线
                            {type : 'average', name: '平均值'}
                        ]
                    }
                }
            ]
        });
    }



//        myChart.setOption(option);
</script>
</html>