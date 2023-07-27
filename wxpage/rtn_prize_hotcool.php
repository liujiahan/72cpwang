<?php

require_once dirname(__FILE__) . '/../include/config.inc.php';
require_once dirname(__FILE__) . '/../wxpage/core/core.func.php';
require_once dirname(__FILE__) . '/../wxpage/core/suanfa.func.php';

LoginCheck();

$data3d = array();
$allBlue = array();
for ($i = 1; $i < 17; $i++) {
    $allBlue[] = $i;
}
$cpnum = isset($cpnum) ? $cpnum : 30;
$jq_cpid = 0;
$minid = 0;
$maxid = 0;
$ssqid = array();
$tmp3d = array();
$dosql->Execute("SELECT * FROM `#@__caipiao_blue_fx` ORDER BY cp_dayid DESC LIMIT {$cpnum}");
$ssqindex = array();
$i = 1;
while ($row = $dosql->GetArray()) {
    if ($jq_cpid == 0) {
        $jq_cpid = $row['cp_dayid'] - $cpnum;
    }
    $tmp = array(
        'cp_dayid' => $row['cp_dayid'],
        'blue_num' => $row['blue_num'],
        'blue_status' => $row['blue_status']=='冷'?16:10,
    );
    array_push($tmp3d, $tmp);
    $ssqid[] = $row['cp_dayid'];
    $tmpid = $row['cp_dayid'] - $jq_cpid;
    if ($minid == 0) {
        $minid = $tmpid;
    }
    if ($tmpid < $minid) {
        $minid = $tmpid;
    }
    if ($tmpid > $maxid) {
        $maxid = $tmpid;
    }
    $ssqindex[] = $i;
    $i++;
}
$ssqid = array_reverse($ssqid);
$tmp3d = array_reverse($tmp3d);
foreach ($tmp3d as $k => $row) {
    foreach ($allBlue as $blue) {
        if ($blue == $row['blue_num']) {
            array_push($data3d, array(
                $row['blue_num'],
                $ssqindex[$k],
                $row['blue_status']
            ));
        } else {
            array_push($data3d, array(
                $blue,
                $ssqindex[$k],
                0
            ));
        }
    }
}

?>
<!DOCTYPE html>
<html style="height: 100%">
   <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>蓝号冷热3D图</title>
   </head>
   <body style="height: 100%; margin: 0">
       <div id="container" style="height: 100%"></div>
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/echarts/echarts.min.js"></script>
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/echarts-gl/echarts-gl.min.js"></script>
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/echarts-stat/ecStat.min.js"></script>
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/echarts/extension/dataTool.min.js"></script>
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/echarts/map/js/china.js"></script>
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/echarts/map/js/world.js"></script>
       <!-- <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=xfhhaTThl11qYVrqLZii6w8qE5ggnhrY&__ec_v__=20190126"></script> -->
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/echarts/extension/bmap.min.js"></script>
       <script type="text/javascript" src="https://echarts.baidu.com/gallery/vendors/simplex.js"></script>
       <script type="text/javascript">
var dom = document.getElementById("container");
var myChart = echarts.init(dom);
var app = {};
option = null;

var hours = '<?php echo json_encode($ssqid) ?>';
var days = '<?php echo json_encode($allBlue) ?>';

var data = [];
<?php foreach ($data3d as $v) { ?>
  data.push([<?php echo $v[0] ?>, <?php echo $v[1] ?>, <?php echo $v[2] ?>]);
<?php } ?>

// console.log(data);
option = {
    tooltip: {},
    visualMap: {
        max: 20,
        inRange: {
            color: ['#313695', '#4575b4', '#74add1', '#abd9e9', '#e0f3f8', '#ffffbf', '#fee090', '#fdae61', '#f46d43', '#d73027', '#a50026']
        }
    },
    xAxis3D: {
        type: 'value',//value,category,time.log
        show:false,//是否显示 x 轴。\
        name:'双色球期数[起点是<?php echo $jq_cpid ?>期]',//坐标轴名称
        grid3DIndex:0,//坐标轴使用的 grid3D 组件的索引
        nameTextStyle:{//‘x轴’样式设置
            color:'red'
        },
        nameGap:20,//坐标轴名称与轴线之间的距离，注意是三维空间的距离而非屏幕像素值。
        min:0,//可以设置成特殊值 'dataMin'，此时取数据在该轴上的最小值作为最小刻度。适用于值在类目轴中，也可以设置为类目的序数（如类目轴 data: ['类A', '类B', '类C'] 中，序数 2 表示 '类C'。也可以设置为负数，如 -3）
        max:<?php echo $cpnum ?>,
        axisLine:{
            show:true,
            interval:1,//(此处无效？)坐标轴刻度标签的显示间隔，在类目轴中有效。如果设置为 1，表示『隔一个标签显示一个标签』，如果值为 2，表示隔两个标签显示一个标签，以此类推。
            lineStyle:{
                color:'red'
            },
        },
        axisLabel:{
            show:true,
            margin:5,
            interval:3,//可控制坐标轴刻度标签的显示间隔，在类目轴中有效。
            formatter:"{value}期"//自定义x轴显示数据标签格式
        },
        axisTick:{},
        axisPointer:{
            // label:'',//标签
            show:true//是否显示坐标轴指示线。
        }
    },
    yAxis3D: {
        type: 'value',
        name:'蓝号',//坐标轴名称
    },
    zAxis3D: {
        type: 'value',
        name:'冷热[高冷第热]',//坐标轴名称
        min:0,//可以设置成特殊值 'dataMin'，此时取数据在该轴上的最小值作为最小刻度。适用于值在类目轴中，也可以设置为类目的序数（如类目轴 data: ['类A', '类B', '类C'] 中，序数 2 表示 '类C'。也可以设置为负数，如 -3）
        max:20,
    },
    grid3D: {
        show:true,//是否显示三维迪卡尔坐标
        boxWidth: 200,
        boxDepth: 80,
        axisLine:{//坐标轴轴线(线)控制
                show:true,//该参数需设为true
                // interval:200,//x,y坐标轴刻度标签的显示间隔，在类目轴中有效。
                lineStyle:{//坐标轴样式
                    color:'red',
                    opacity:1,//(单个刻度不会受影响)
                    width:2//线条宽度
                }
            },
        axisLabel:{
                show:true,//是否显示刻度  (刻度上的数字，或者类目)
                //   
                interval:5,//坐标轴刻度标签的显示间隔，在类目轴中有效。
                formatter:function(v){
                    return v;
                },
 
                textStyle:{
                    // color:'#000',//刻度标签样式，见图黑色刻度标签
                    color: function (value, index) {
                        return value >= 6? 'green' : 'red';//根据范围显示颜色，主页为值有效
                    },
                    //  borderWidth:"",//文字的描边宽度。
                    //  borderColor:'',//文字的描边颜色。
                    fontSize:14,//刻度标签字体大小
                    fontWeight:'',//粗细
                }
            },
        // axisTick:{
        //         show:true,//是否显示出刻度
        //         // interval:100,//坐标轴刻度标签的显示间隔，在类目轴中有效
        //         length:5,//坐标轴刻度的长度
        //         lineStyle:{//举个例子，样式太丑将就
        //             color:'#000',//颜色
        //             opacity:1,
        //             width:5//厚度（虽然为宽表现为高度），对应length*(宽)
        //         }
        //     },
        splitLine:{//平面上的分隔线。
                show:true,//立体网格线  
                // interval:100,//坐标轴刻度标签的显示间隔，在类目轴中有效
                splitArea:{
                    show:true,
                    // interval:100,//坐标轴刻度标签的显示间隔，在类目轴中有效
                    areaStyle:{
                        color:['rgba(250,250,250,0.3)','rgba(200,200,200,0.3)','rgba(250,250,250,0.3)','rgba(200,200,200,0.3)']
                    }
                },
            },
            axisPointer:{//坐标轴指示线。
                show:true,//鼠标在chart上的显示线
                // lineStyle:{
                //     color:'#000',//颜色
                //     opacity:1,
                //     width:5//厚度（虽然为宽表现为高度），对应length*(宽)
                // }
            },
            //整个chart背景，可为自定义颜色或图片
            //         environment: 'asset/starfield.jpg'
            // // 配置为纯黑色的背景
            // environment: '#000'
            environment: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                offset: 0, color: '#00aaff' // 天空颜色
            }, {
                offset: 0.7, color: '#998866' // 地面颜色
            }, {
                offset: 1, color: '#998866' // 地面颜色
            }], false),

            viewControl: {//viewControl用于鼠标的旋转，缩放等视角控制。(以下适合用于地球自转等)
                // projection: 'orthographic'//默认为透视投影'perspective'，也支持设置为正交投影'orthographic'。
                // autoRotate:true,//会有自动旋转查看动画出现,可查看每个维度信息
                // autoRotateDirection:'ccw',//物体自传的方向。默认是 'cw' 也就是从上往下看是顺时针方向，也可以取 'ccw'，既从上往下看为逆时针方向。
                // autoRotateSpeed:12,//物体自传的速度
                // autoRotateAfterStill:2,//在鼠标静止操作后恢复自动旋转的时间间隔。在开启 autoRotate 后有效。
                // distance:300,//默认视角距离主体的距离(常用)
                // alpha:1,//视角绕 x 轴，即上下旋转的角度(与beta一起控制视野成像效果)
                // beta:10,//视角绕 y 轴，即左右旋转的角度。
                // center:[]//视角中心点，旋转也会围绕这个中心点旋转，默认为[0,0,0]。
                // zlevel://组件所在的层。
 
                animation:true
            },
            light: {//光照相关的设置
                main: {
                    color:'#fff',//光照颜色会与所设置颜色发生混合
                    intensity:1.2,//主光源的强度(光的强度)
                    shadow: true,//主光源是否投射阴影。默认关闭。
                    // alpha:0//主光源绕 x 轴，即上下旋转的角度。配合 beta 控制光源的方向(跟beta结合可确定太阳位置)
                    // beta:10//主光源绕 y 轴，即左右旋转的角度。
                },
                ambient: {//全局的环境光设置。
                    intensity: 0.3,
                    color:'#fff'//影响柱条颜色
                },
                // ambientCubemap: {//会使用纹理作为光源的环境光
                //  texture: 'pisa.hdr',
                // // 解析 hdr 时使用的曝光值
                // exposure: 1.0
                // }
            },
            // postEffect:{//后处理特效的相关配置，后处理特效可以为画面添加高光，景深，环境光遮蔽（SSAO），调色等效果。可以让整个画面更富有质感。
            //     show:true,
            //     bloom:{
            //         enable:true//高光特效,适合地球仪
            //     }
            // }
            //调整位置(常用)
            top:0,//组件的视图离容器上侧的距离
            // right:10,
            //  bottom:0,
            //组件的视图宽度。
            // width:100,
            // height:200
        viewControl: {
            // projection: 'orthographic'
        },
        light: {
            main: {
                intensity: 1.2,
                shadow: true
            },
            ambient: {
                intensity: 0.3
            }
        }
    },
    series: [{
        type: 'bar3D',
        data: data.map(function (item) {
            return {
                value: [item[1], item[0], item[2]],
            }
        }),
        bevelSize:0.5,//柱子的倒角尺寸
        bevelSmoothness:0,//柱子倒角的光滑/圆润度，数值越大越光滑/圆润。
        // shading: 'lambert',//

        itemStyle:{//柱条样式
            color:'#000',
            // opacity:0.5
        },
        
        label: {
            show:true,//柱子的样式，包括颜色和不透明度。
            distance:0,//标签距离图形的距离
            // formatter:function(value){
            //   return value;  
            // },
            textStyle: {//标签的字体样式。
                fontSize: 16,
                borderWidth: 1
            }
        },

        emphasis: {
            label: {
                textStyle: {
                    fontSize: 20,
                    color: '#900'
                }
            },
            itemStyle: {
                color: '#900'
            }
        }
    }]
};
if (option && typeof option === "object") {
    myChart.setOption(option, true);
}
       </script>
   </body>
</html>