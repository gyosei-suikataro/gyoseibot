<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>チャットボットテスト</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery.bootgrid.css" rel="stylesheet" />
<link href="css/botui.min.css" rel="stylesheet" />
<link href="css/botui-theme-default.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<!-- <script src="//cdn.jsdelivr.net/vue/latest/vue.min.js"></script> -->
<script src="//npmcdn.com/vue@2.0.5/dist/vue.min.js"></script>
<script src="//unpkg.com/botui/build/botui.min.js"></script>

<!--
<script src="https://embed.small.chat/T7ZNUHSENG7ZM4UH8B.js" async></script>
-->
</head>
<body>
<div id="header"></div>
<div class="container">
	<h1>チャットボットテスト</h1>
	<input id="btn_clear" type="button" class="btn btn-default" value="クリア" onclick="dispclear()">
	<div class="botui-app-container" id="chat-app">
    	<!-- チャットの表示  -->
    	<bot-ui></bot-ui>
	</div>
</div>
</body>
<script>
$(function(){
	$("#header").load("header.html");

	var url = 'https://api.github.com/search/repositories?q=';
	var msgIndex, key;
	var botui = new BotUI('chat-app');
	var user = "webtest";
	var lang = "";
	var sex = "0";
	var age = "999";
	var region = "";
	var search = "";
	var message = "";
	var sexN = "";

	attributeSearch();

	  //初期メッセージ
	  botui.message.bot({
	    content: 'こ<br>んにちは！'
	  }).then(init);

	  function init() {
		  botui.message.bot({
			  delay: 1500,  //メッセージの表示タイミングをずらす
		      content: 'はじめにテストするボットを選択してください'
		  }).then(function() {
		      return botui.action.button({
		        delay: 1000,
		        action: [{
		          text: '属性登録',
		          value: '属性登録'
		        }, {
		          text: '検診相談',
		          value: '検診相談'
		        }, {
		          text: 'その他のお問い合わせ',
			      value: 'その他のお問い合わせ'
		        }]
		      });
		  }).then(function(res) {
			  switch (res.value){
			  case '属性登録':
				message = 'それでは、以下のリンクより属性登録をお願いします。';
				attribute();
			    break;
			  case '検診相談':
				kenshin();
			    break;
			  case 'その他のお問い合わせ':
				sonota();
			    break;
			}
				/*
			  return botui.message.bot({
			  	delay: 1500,
			  	content: '「' + res.value + '」ですね。かしこまりました。'


			  })
			  */
		  })
	  }

	  //属性登録
	  function attribute(){
		  botui.message.bot({
			  delay: 1000,
			  content: message
		  }).then(function() {
			  var attrurl = "";
			  if (lang == "02"){
				  attrurl = "https://gyoseibot.herokuapp.com/attribute_en.php?user=";
			  }else{
				  attrurl = "https://gyoseibot.herokuapp.com/attribute.php?user=";
			  }
			  if(age < 10){
					age = "00" + age;
				}else{
					if(age < 100){
						age = "0" + age;
					}
			  }
			  botui.message.add({
			        delay: 1000,
			        content: '[属性登録](' + attrurl + user.substr(0, 1) + sex + user.substr(1, 1) + age + user.substr(2, 1) + region + user.substr(3) + ')^'
			  });
		  }).then(init);
	  }

	  //属性検索
	  function attributeSearch(){
		var param = { "user": user };
		$.ajax({
            type: "GET",
            url: "attsearch.php",
            data: param,
            crossDomain: false,
            dataType : "json",
            scriptCharset: 'utf-8',
            async: false
        }).done(function(data){
        	lang = data.lang;
        	sex = data.sex;
        	age = data.age;
        	region = data.region;
        	search = data.search;
        }).fail(function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        });
	  }

	  //検診相談
	  function kenshin(){
		  //属性登録チェック
		  attributeSearch();
		  if(sex == "0" || age == "999"){
			  message = '申し訳ありませんが、先に以下のリンクより属性登録をお願いします。';
			  attribute();
			  return;
		  }

		  if(sex == "1"){
			sexN = "男";
		  }
		  if(sex == "2"){
			sexN = "女";
		  }

		  callWatson("1", "0", age + "の" + sexN)
		  botui.message.bot({
			  delay: 1000,
			  content: message
		  }).then(function() {
			  return botui.action.text({
			        delay: 1000,
			        action: {
			          placeholder: '入力してください'
			        }
			  });
		  })
	  }

	  //その他のお問い合わせ
	  function sonota(){
		  botui.message.bot({
			  delay: 1000,
			  content: 'それでは、質問をお願いします。'
		  }).then(function() {
			  return botui.action.text({
			        delay: 1000,
			        action: {
			          placeholder: '質問を入力してください'
			        }
			  });
		  })
	  }

	  //Watson呼び出し
	  function callWatson(param, kbn, text){
		  var param = { "user": user , "param": param , "kbn": kbn, "text": text };
			$.ajax({
	            type: "GET",
	            url: "cw.php",
	            data: param,
	            crossDomain: false,
	            dataType : "json",
	            scriptCharset: 'utf-8',
	            async: false
	        }).done(function(data){
	        	message = data.text;
	        }).fail(function(XMLHttpRequest, textStatus, errorThrown){
	            alert(errorThrown);
	        });
	  }

	  function init2() {
	    botui.message.bot({
	      delay: 1500,  //メッセージの表示タイミングをずらす
	      content: '気になるキーワードで、GitHubの総リポジトリ数をお答えします！'
	    }).then(function() {

	      //キーワードの入力
	      //「return」を記述して、ユーザーからの入力待ち状態にする
	      return botui.action.text({
	        delay: 1000,
	        action: {
	          placeholder: '例：javascript'
	        }
	      });
	    }).then(function(res) {

	      //入力されたキーワードを取得する
	      key = res.value;
	      getRepositories(key);

	      //ローディング中のアイコンを表示
	      botui.message.bot({
	        loading: true
	      }).then(function(index) {

	        //ローディングアイコンのindexを取得
	        //このindexを使ってメッセージ情報を更新する
	        //（更新しないとローディングアイコンが消えないため…）
	        msgIndex = index;
	      });
	    });
	  }


	  //GitHubのリポジトリを取得する処理
	  function getRepositories(keyword) {
	    var xhr = new XMLHttpRequest();

	    xhr.open('GET', url + keyword);
	    xhr.onload = function() {
	      var result = JSON.parse(xhr.responseText);

	      //取得したリポジトリ数をshowMessage()に代入する
	      showMessage(result.total_count);
	    }
	    xhr.send();
	  }


	  //リポジトリ総数をメッセージに表示する処理
	  function showMessage(totalCount) {

	    //ローディングアイコンのindexを使ってメッセージを書き換える
	    botui.message.update(msgIndex, {
	      content: key + 'のリポジトリ総数は、' + totalCount + '個です！'
	    }).then(function() {
	      return botui.message.bot({
	        delay: 1500,
	        content: 'まだ続けますか？'
	      })
	    }).then(function() {

	      //「はい」「いいえ」のボタンを表示
	      return botui.action.button({
	        delay: 1000,
	        action: [{
	          icon: 'circle-thin',
	          text: 'はい',
	          value: true
	        }, {
	          icon: 'close',
	          text: 'いいえ',
	          value: false
	        }]
	      });
	    }).then(function(res) {

	      //「続ける」か「終了」するかの条件分岐処理
	      res.value ? init() : end();
	    });
	  }


	  //プログラムを終了する処理
	  function end() {
	    botui.message.bot({
	      content: 'ご利用ありがとうございました！'
	    })
	  }
});

function dispclear(){
	location.reload();
}
</script>
</html>

