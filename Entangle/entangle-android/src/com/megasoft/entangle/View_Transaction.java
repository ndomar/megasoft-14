package com.megasoft.entangle;

import java.util.concurrent.ExecutionException;

import android.app.Activity;
import android.os.Bundle;
import android.widget.TextView;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;

public class View_Transaction extends Activity {
	
	
	@Override
    public void onCreate(Bundle savedInstanceState) {         

       super.onCreate(savedInstanceState);    
       setContentView(R.layout.view_transaction);
       
       TextView amount = (TextView) findViewById(R.id.textView5);
       TextView request = (TextView) findViewById(R.id.textView6);
       TextView requester = (TextView) findViewById(R.id.textView7);
       
       GetRequest get = new GetRequest("http://entangle2.apiary.io/transaction/2");
       JSONObject json = new JSONObject();
       
       get.addHeader("sessionID", "testest");
      
       try {
    	get.execute("http://entangle2.apiary.io/transaction/2");   
		json = new JSONObject(get.get());
		
		amount.setText(json.getString("amount"));
		request.setText(json.getString("requestID"));
		requester.setText(json.getString("requesterID"));
		
	} catch (InterruptedException e) {
		// TODO Auto-generated catch block
		e.printStackTrace();
	} catch (ExecutionException e) {
		// TODO Auto-generated catch block
		e.printStackTrace();
	} catch (JSONException e) {
		// TODO Auto-generated catch block
		e.printStackTrace();
	}
     
   } 
	
}
