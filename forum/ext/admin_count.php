<?php
require('../ext_public/phplot/phplot.php');
$config=include('../Conf/config.php');
$mix_config=include('../Conf/mix.config.php');
include('../ext_public/database.php');
$arr=mysql_fetch_array(mysql_query("select * from " . $config['db_prefix'] . "count where id=1"));
$user_count=unserialize($arr['user_count']);
$post_count=unserialize($arr['post_count']);
$data = array(
  array('Monday',empty($user_count['a'])?0:$user_count['a'],empty($post_count['a'])?0:$post_count['a']),
  array('Tuesday',empty($user_count['b'])?0:$user_count['b'],empty($post_count['b'])?0:$post_count['b']),
  array('Wednesday',empty($user_count['c'])?0:$user_count['c'],empty($post_count['c'])?0:$post_count['c']),
  array('Thursday',empty($user_count['d'])?0:$user_count['d'],empty($post_count['d'])?0:$post_count['d']),
  array('Friday',empty($user_count['e'])?0:$user_count['e'],empty($post_count['e'])?0:$post_count['e']),
  array('Saturday',empty($user_count['f'])?0:$user_count['f'],empty($post_count['f'])?0:$post_count['f']),
  array('Sunday',empty($user_count['g'])?0:$user_count['g'],empty($post_count['g'])?0:$post_count['g'])
);
$p = new PHPlot(761, 300);
//$p->SetDefaultTTFont('');
$p->SetTitle('New Users/Posts in a Week');
 
# Select the data array representation and store the data:
$p->SetDataType('text-data'); //设置使用的数据类型，在这个里面可以使用多种类型。
$p->SetDataValues($data); //把一个数组$data赋给类的一个变量$this->data_values.要开始作图之前调用。
$p->SetPlotType('lines'); //选择图表类型为线性.可以是bars,lines,linepoints,area,points,pie等。
 
$p->SetPlotAreaWorld(0, 0, 7, $mix_config['admin_count_num']);  //设置图表边距
$p->SetXTickPos('none');  
# Select an overall image background color and another color under the plot:
$p->SetBackgroundColor('#ffffcc'); //设置整个图象的背景颜色。
$p->SetDrawPlotAreaBackground(True); //设置节点区域的背景
$p->SetPlotBgColor('#ffffff'); //设置使用SetPlotAreaPixels()函数设定的区域的颜色。
$p->SetLineWidth(3);  //线条宽度
# Draw lines on all 4 sides of the plot:
$p->SetPlotBorderType('full');  //设置线条类型
 
# Set a 3 line legend, and position it in the upper left corner:
$p->SetLegend(array('Users', 'Posts')); //显示在一个图列框中的说明
$p->SetLegendWorld(0.3, ($mix_config['admin_count_num']-5)); //设定这个文本框位置
 
# Generate and output the graph now:
$p->DrawGraph();
?>