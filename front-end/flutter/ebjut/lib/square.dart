import 'package:flutter/material.dart';
import 'package:ebjut/moment.dart';

class Square extends StatelessWidget {
//  final List<Moment> _allMoments = Moment.allMoments();
  final _moms = <Moment>[];
  final _biggerFont = const TextStyle(fontSize: 18.0); //用于标识字符串的样式

  @override
  State<StatefulWidget> createState() => new ListState();

  @override
  Widget build(BuildContext context) {
    return new MaterialApp(
      title: "Moment Square",
      theme: new ThemeData(
        primaryColor: Colors.blue,
      ),
      home: new _List(), //定义子组件为有状态控件RandomWords类的实例
    );
  }
}

class _List extends StatefulWidget {
  // @ovrerride
  createState() => new ListState();
}

class ListState extends State<_List> {
//  static const loadingTag ="##loading##";
//  var _words = <String>[loadingTag];

//  @override
//  void initState(){
//    List<Moment> _allMoments = Moment.allMoments();
//    _retrieveData(_allMoments,1);
//  }

  @override
  Widget build(BuildContext context) {
    return new Scaffold(
      appBar: new AppBar(
        title: new Text("Moment Square"),
      ),
      body: _buildBody(),
    );
  }

  Widget _buildBody() {
    List<Moment> _allMoments = Moment.allMoments();
    List<Widget> _list = new List();

    for (int i = 0; i < _allMoments.length; i++) {
      _list.add(new Padding(
          padding: new EdgeInsets.all(5.0),
          child: ListBody(children: <Widget>[
            new Row(
              children: <Widget>[
                new Padding(
                    padding: new EdgeInsets.all(15.0),
                    child: new CircleAvatar(
                      backgroundImage: AssetImage(
                        _allMoments[i].avatar,
                      ),
                    )),
                new Text(
                  _allMoments[i].username.toString(),
                  style: TextStyle(fontSize: 18.0),
                ),
              ],
            ),
//                new Image.asset(
//                  _allMoments[i].avatar, width: 40.0, height: 40,),
//
//                new Text(
//                  _allMoments[i].username.toString(),
//                  style: TextStyle(fontSize: 18.0),
//                ),
            new Padding(
              padding: new EdgeInsets.all(5.0),
              child: new Text(_allMoments[i].message),
            ),
            // new Text(_allMoments[i].message),
            new Image.asset(
              _allMoments[i].image,
              width: 80.0,
              height: 80.0,
              // fit: BoxFit.cover,
              //scale:10.0,
            )
          ])
          //child: new Text(_allMoments[i].username.toString()),
          //child: new Text(_allMoments[i].message),
          ));
    }

    var divideList =
        ListTile.divideTiles(context: context, tiles: _list).toList();
    return new Scrollbar(
      child: new ListView(
        children: divideList, // 添加分割线/
      ),
    );

//      return new Center(
//
//          child:
//          ListView(
//            children: <Widget>[
//              new Text(_allMoments[i].username.toString(),
//                style: TextStyle(fontSize: 18.0),),
//              new Text(_allMoments[i].message),
//              new Image.asset(
//                _allMoments[i].image,
//                 width: 100.0,
//                  height: 100.0,
//                fit: BoxFit.cover,)
//            ],
//          )
//      );
  }
}

//    return ListView.separated(
//
//      itemCount: _words.length,
//      itemBuilder: (context, index) {
//        //如果到了表尾
//        if (_words[index] == loadingTag) {
//          //不足100条，继续获取数据
//          if (_words.length - 1 < 100) {
//            //获取数据
//            _retrieveData(_allMoments,_words.length);
//            //加载时显示loading
//            return Container(
//              padding: const EdgeInsets.all(16.0),
//              alignment: Alignment.center,
//              child: SizedBox(
//                  width: 24.0,
//                  height: 24.0,
//                  child: CircularProgressIndicator(strokeWidth: 2.0)
//              ),
//            );
//          } else {
//            //已经加载了100条数据，不再获取数据。
//            return Container(
//                alignment: Alignment.center,
//                padding: EdgeInsets.all(16.0),
//                child: Text("没有更多了", style: TextStyle(color: Colors.grey),)
//            );
//          }
//        }
//        //显示单词列表项
//        return ListTile(title: Text(_words[index]));
//      },
//      separatorBuilder: (context, index) => Divider(height: .0),
//    );

//  void _retrieveData(_allMoments,index) {
//    Future.delayed(Duration(seconds: 2)).then((e) {
//      _words.insertAll(_words.length - 1,
//          //每次生成20个单词
//         // generateWordPairs().take(20).map((e) => e.asPascalCase).toList()
//        // PersonalState(_allMoments),
//        _allMoments.get(index+1),
//      );
//      setState(() {
//        //重新构建列表
//      });
//    });
//  }
