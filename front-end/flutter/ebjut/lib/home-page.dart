import 'package:flutter/material.dart';
import 'articles.dart';
import 'announcement.dart';
import'./other_page.dart';

class MyHomePage extends StatefulWidget {
  @override
  _MyHomePageState createState() => _MyHomePageState();
}

class _MyHomePageState extends State<MyHomePage>{
  final list=[Announcement(),Articles()];
  TabController _tabController;

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {

    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: TabBar(
            tabs: [
              Tab(text: "Announcement"),
              Tab(text: "Articles")
            ],
          ),
        ),
        drawer:
        new Drawer(     //侧边栏按钮Drawer
          child: new ListView(
            children: <Widget>[
              new UserAccountsDrawerHeader(   //Material内置控件
                accountName: new Text('UserName'), //用户名
                accountEmail: new Text('example@126.com'),  //用户邮箱
                currentAccountPicture: new GestureDetector( //用户头像
                  onTap: () => print('current user'),
                  child: new CircleAvatar(    //圆形图标控件
                    backgroundImage: new NetworkImage('https://img-blog.csdn.net/20160510110020141'),//图片调取自网络
                  ),
                ),
                decoration: new BoxDecoration(    //用一个BoxDecoration装饰器提供背景图片
                  image: new DecorationImage(
                    fit: BoxFit.fill,
                    // image: new NetworkImage('https://raw.githubusercontent.com/flutter/website/master/_includes/code/layout/lakes/images/lake.jpg')
                    //可以试试图片调取自本地。调用本地资源，需要到pubspec.yaml中配置文件路径
                    image: new ExactAssetImage('images/lake.jpg'),
                  ),
                ),
              ),
              new ListTile(   //第一个功能项
                  title: new Text('First Page'),
                  trailing: new Icon(Icons.arrow_upward),
                  onTap: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).push(new MaterialPageRoute(builder: (BuildContext context) => new OtherPage('First Page')));
                  }
              ),
              new ListTile(   //第二个功能项
                  title: new Text('Second Page'),
                  trailing: new Icon(Icons.arrow_right),
                  onTap: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).push(new MaterialPageRoute(builder: (BuildContext context) => new OtherPage('Second Page')));
                  }
              ),
              new ListTile(   //第二个功能项
                  title: new Text('Second Page'),
                  trailing: new Icon(Icons.arrow_right),
                  onTap: () {
                    Navigator.of(context).pop();
                    Navigator.of(context).pushNamed('/a');
                  }
              ),
              new Divider(),    //分割线控件
              new ListTile(   //退出按钮
                title: new Text('Close'),
                trailing: new Icon(Icons.cancel),
                onTap: () => Navigator.of(context).pop(),   //点击后收起侧边栏
              ),
            ],
          ),
        ),
        body: TabBarView(
          children: list,
          physics: new NeverScrollableScrollPhysics(), //prevent horizontal scroll
        ),
      ),
    );


  }
}