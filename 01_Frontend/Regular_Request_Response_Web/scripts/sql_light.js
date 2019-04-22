function setUserData(tx) {
    tx.executeSql('SELECT * FROM user', [], function (tx, results) {
        dbUser['oname'] = results.rows.item(0).name
        dbUser['osurname'] = results.rows.item(0).osurname
        dbUser['login'] = results.rows.item(0).ologin
        dbUser['email'] = results.rows.item(0).oemail
        dbUser['currency1'] = results.rows.item(0).currency1
        dbUser['currency2'] = results.rows.item(0).currency2
        dbUser['currency3'] = results.rows.item(0).currency3
        tx.executeSql('SELECT DISTINCT friend_id,friend_full_name FROM balance', [], userFriends, errorCB);
    });
}



function CurrencyBalance(){
    db.transaction(function (tx) {
        tx.executeSql('SELECT SUM(value) as sum,value,type,curency,ownType FROM transactions  WHERE type="currency" and archive!="1" GROUP BY type,ownType,curency', [], function(tx,results){
            $('#iOweDetials .currencyDetails,#iAmBeingOweDetails .currencyDetails').each(function(){
                $(this).remove();
            })
            var len = results.rows.length;
            var curenciesList=new Array();
            $('td#balanceSumIOwe,td#balanceSumIamBeingOwe').html('');
            for(var i=0; i<len; i++){
                type=results.rows.item(i).type;
                if(type=='currency')
                {
                    value=results.rows.item(i).value;
                    curency=results.rows.item(i).curency;
                    ownType=results.rows.item(i).ownType;
                    sum=results.rows.item(i).sum;
                    sum=Math.abs(sum);
                    if(ownType=='iOwe'){
                        $('td#balanceSumIOwe').append('<div><span>'+curency+' '+sum+'</span></div>');
                        $('#iOweDetials').prepend('<div class="currencyDetails">'+curency+' '+sum+'</div>');
                    }else{
                        $('td#balanceSumIamBeingOwe').append('<div><span>'+curency+' '+sum+'</span></div>');
                        $('#iAmBeingOweDetails').prepend('<div class="currencyDetails">'+curency+' '+sum+'</div>');
                    }
                }
            }
            $('#balanceSumIamBeingOwe div,#balanceSumIOwe div').textfill({ maxFontPixels: 70});
            $('td#balanceSumIOwe,td#balanceSumIamBeingOwe')
                .cycle({
                    fx:     'fade',
                    speed:  'slow',
                    timeout: 3000
                });
        })
    })
}


