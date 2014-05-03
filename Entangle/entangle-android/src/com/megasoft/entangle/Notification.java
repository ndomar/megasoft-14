package com.megasoft.entangle;

import java.util.concurrent.ExecutionException;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PutRequest;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;


public class Notification extends Activity {

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.notification);
		
		TextView text = (TextView) findViewById(R.id.textView1);
		text.setText("This is a notification....");
		
		//Check if the notification new or seen.
		
		checkStatus(text);
				
		text.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent i = new Intent(Notification.this , MainActivity.class);
				startActivity(i);
				
				//set the notification as seen in the backend
				
				setSeen();
			}
		});
				
	}
	/**
	 * This method is used to set the notification status as seen
	 * @author Mohamed Ayman
	 */
	
	public void setSeen() {
		
		JSONObject json = new JSONObject();

		PutRequest putRequest = new PutRequest("http://entangle2.apiary.io/notification/1/set-seen");
		putRequest.addHeader("sessionID", "testest");
		putRequest.setBody(json);
		putRequest.execute("http://entangle2.apiary.io/1/notification/set-seen");
	}
	/**
	 * This method checks the status of the notification(new or seen).
	 * @param text
	 * @author Mohamed Ayman
	 */
	public void checkStatus(TextView text) {
		GetRequest getRequest = new GetRequest("http://entangle2.apiary.io/notification/1/get-seen");
		getRequest.addHeader("sessionID" , "testest");
		
		JSONObject json = new JSONObject();
		
		try {
			getRequest.execute("http://entangle2.apiary.io/notification/1/get-seen");
			json = new JSONObject(getRequest.get());
		 
			//Different displays for seen and new notifications
			if(json.optInt("seen") == 1) { 
				text.setTypeface(null, Typeface.NORMAL);
				text.setTextColor(Color.GRAY);
			} else {
				text.setTypeface(null, Typeface.BOLD_ITALIC);
			} 
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ExecutionException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}  
}
