import 'package:flutter/material.dart';
import 'login.dart';
import 'package:dio/dio.dart';
import 'dart:convert';
import 'dart:async';
import 'package:flutter_spinkit/flutter_spinkit.dart';

class Register extends StatefulWidget {
  @override
  _Register createState() => _Register();
}

class _Register extends State<Register> {
  final _formKey = GlobalKey<FormState>();
  String sID, password, username, phone, nickname, email;
  var msg,code;
  String registerText="Register";
  var textController = TextEditingController();
  var textController2 = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: Form(
            key: _formKey,
            child: ListView(
              padding: EdgeInsets.symmetric(horizontal: 22.0),
              children: <Widget>[
                SizedBox(
                  height: kToolbarHeight,
                ),
                buildTitle(),
                SizedBox(height: 20.0),
                buildIDTextField(context),
                SizedBox(height: 20.0),
                buildUserNameTextField(context),
                SizedBox(height: 20.0),
                buildPasswordTextField(context),
                SizedBox(height: 20.0),
                buildPhoneNumberTextField(context),
                SizedBox(height: 20.0),
                buildNicknameTextField(context),
                SizedBox(height: 20.0),
                buildEmailTextField(context),
                SizedBox(height: 60.0),
//                buildLoading(),
                buildRegisterButton(context),
                SizedBox(height: 30.0),
              ],
            )));
  }

  Align buildRegisterButton(BuildContext context) {
    return Align(
      child: SizedBox(
        height: 45.0,
        width: 270.0,
        child: RaisedButton(
          child: Text(
            registerText,
            style: TextStyle(fontSize: 20.0, color: Colors.white),
          ),
          color: Colors.black,
          onPressed: () {
            if (_formKey.currentState.validate()) {
              _formKey.currentState.save();
              changeState();
              print('\n----- ID:$sID, Username: $username, Password:$password, Phone Number:$phone, Nickname: $nickname, Email: $email ----- \n');
              post();
              new Timer(Duration(milliseconds:1000), () => print("$msg,$code"));
              new Timer(Duration(milliseconds:2000), () => toLogin());
            }
          },
          shape: StadiumBorder(side: BorderSide()),
        ),
      ),
    );
  }

  TextFormField buildPasswordTextField(BuildContext context) {
    return TextFormField(
      onSaved: (String value) => password = value,
      validator: (String value) {
        if (value.isEmpty) {
          return 'Please input a password';
        }
      },
      decoration: InputDecoration(
          labelText: 'Password',
      ),
//      controller: textController,
    );
  }

  TextFormField buildIDTextField(BuildContext context) {
    return TextFormField(
      onSaved: (String id) => sID = id,
      decoration: InputDecoration(
        labelText: 'Student ID',
      ),
      controller: textController,
      validator: (String value) {
        var emailReg = RegExp(r"^\d{8}$"); // restrict ID is a eight-digit number
        if (!emailReg.hasMatch(value)) {
          return 'Invalid format';
        }
      },
    );
  }

  TextFormField buildPhoneNumberTextField(BuildContext context) {
    return TextFormField(
      decoration: InputDecoration(
        labelText: 'Phone Number',
      ),
//      controller: textController,
      validator: (String value) {
        var emailReg = RegExp(r"^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$"); // restrict Phone number
        if (!emailReg.hasMatch(value)) {
          return 'Invalid format';
        }
      },
      onSaved: (String value) => phone = value,
    );
  }

  TextFormField buildUserNameTextField(BuildContext context) {
    return TextFormField(
      decoration: InputDecoration(
        labelText: 'User Name',
      ),
//      controller: textController,
      validator: (String value) {
        var emailReg = RegExp(r"^\w{3,20}$"); // restrict User name
        if (!emailReg.hasMatch(value)) {
          return 'Invalid format: only English characters, numbers and _';
        }
      },
      onSaved: (String value) => username = value,
    );
  }

  TextFormField buildNicknameTextField(BuildContext context) {
    return TextFormField(
      decoration: InputDecoration(
        labelText: 'Nickname',
      ),
//      controller: textController,
      validator: (String value) {
        var emailReg = RegExp(r"^[\u4E00-\u9FA5A-Za-z0-9_]+$"); // restrict Nickname
        if (!emailReg.hasMatch(value)) {
          return 'Invalid format: only Chinese&English characters, numbers and _';
        }
      },
      onSaved: (String value) => nickname = value,
    );
  }

  TextFormField buildEmailTextField(BuildContext context) {
    return TextFormField(
      onSaved: (String mail) => email = mail,
      decoration: InputDecoration(
        labelText: 'Email',
      ),
      controller: textController2,
      validator: (String value) {
        var emailReg = RegExp(r"^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$"); // restrict Email
        if (!emailReg.hasMatch(value)) {
          return 'Invalid format';
        }
      },
    );
  }

  Padding buildTitle() {
    return Padding(
      padding: EdgeInsets.all(8.0),
      child: Row(
        children: <Widget>[
          FlatButton(
            child: Text(
              'Login',
              style: TextStyle(fontSize: 20.0, color: Colors.grey),
            ),
            onPressed: () {//on press return new login interface
              Navigator.of(context).pushAndRemoveUntil(
                  new MaterialPageRoute(builder: (context) => new Login() ), (route) => route == null
              );
            },
          ),
          buildTitleLineV(),
          SizedBox(width: 24,),
          Text('Register', style: TextStyle(fontSize: 42.0)),
        ],
      ),
    );
  }

  Padding buildTitleLineV() {
    return Padding(
      padding: EdgeInsets.only(left: 12.0, top: 4.0),
      child: Align(
        alignment: Alignment.center,
        child: Container(
          color: Colors.black,
          width: 2.0,
          height: 35.0,
        ),
      ),
    );
  }

  Padding buildLoading() {
    return Padding(
      padding: EdgeInsets.only(left: 12.0, top: 4.0),
      child: Align(
        alignment: Alignment.center,
        child: SpinKitHourGlass(color: Colors.black),
      ),
    );
  }

  void post() async {
    Response response;
    Dio dio = new Dio();
    FormData formData = new FormData.from({
      "user_login": username,
      "user_password": password,
      "user_nickname": nickname,
      "user_student_id": sID,
      "user_phone": phone,
      "user_email": email,
    });
    response = await dio.post("http://47.88.225.51/api/v1/register.php", data: formData);
    print(response.data);
    Map<String, dynamic> message = json.decode(response.data);
    msg=message['msg'];
    code=message['code'];
  }

  void toLogin(){
    if(code==200){
      setState(() {
        registerText="Successful!";
      });
      new Timer(Duration(milliseconds:2000), () =>
          Navigator.of(context).pushAndRemoveUntil(
              new MaterialPageRoute(builder: (context) => new Login() ), (route) => route == null
          )
      );
//      Navigator.pop(context);
    }else if(code==400){
      setState(() {
        registerText="Already exists!";
      });
      new Timer(Duration(milliseconds:2000), () =>
          Navigator.of(context).pushAndRemoveUntil(
              new MaterialPageRoute(builder: (context) => new Register() ), (route) => route == null
          )
      );
    }
  }

  void changeState(){
    setState(() {
      registerText="waiting...";
    });
  }

}