import 'package:flutter/material.dart';
import 'package:flutter_inappbrowser/flutter_inappbrowser.dart';

class Articles extends StatefulWidget {
  @override
  _Articles createState() => new _Articles();
}

class _Articles extends State<Articles> with AutomaticKeepAliveClientMixin{
  bool get wantKeepAlive => true;

  InAppWebViewController webView;
  double progress = 0;

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
    return Scaffold(
      body: Container(
        child: Column(
          children: <Widget>[
            (progress != 1.0) ? LinearProgressIndicator(value: progress) : null,
            Expanded(
              child: Container(
//                margin: const EdgeInsets.all(10.0),
                child: InAppWebView(
                  onWebViewCreated: (InAppWebViewController controller) {
                    webView = controller;
                    webView.loadFile("webpage/articles.html");
                  },
                  onProgressChanged: (InAppWebViewController controller, int progress) {
                    setState(() {
                      this.progress = progress/100;
                    });
                  },
                ),
              ),
            ),
          ].where((Object o) => o != null).toList(),
        ),
      ),
    );
  }
}