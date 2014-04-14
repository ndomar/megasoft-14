package com.megasoft.entangle;

import java.util.concurrent.ExecutionException;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;
import com.megasoft.requests.PutRequest;

import android.R;
import android.app.Activity;
import android.content.Intent;
import android.content.res.ColorStateList;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

public class Notification extends Activity {

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.notification);
		
		TextView text = (TextView) findViewById(R.id.textView1);
		
		GetRequest getRequest = new GetRequest("");
		getRequest.addHeader("" , "");
		getRequest.execute("");
		JSONObject json;
		try {
			json = new JSONObject(getRequest.get());
			
			if(!json.getBoolean("seen")) { //GET Request to check if the notification is unread
				text.setTypeface(null, Typeface.BOLD_ITALIC);
			} else {
				//text.setTextColor(Color.GRAY);
				markRead(text.getId());
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
		
		
		
		text.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				Intent i = new Intent(Notification.this , View_Transaction.class);
				startActivity(i);
				TextView text = (TextView) findViewById(R.id.textView1);
				markRead(text.getId());
				//set the notification as seen in the backend
				JSONObject json = new JSONObject();
				try {
					json.put("seen", true);
					
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				PutRequest putRequest = new PutRequest("");
				putRequest.addHeader("", "");
				putRequest.setBody(json);
				putRequest.execute("");
				
			}
		});
		
		Button button = (Button) findViewById(R.id.btn1);
	/*	button.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				TextView text = (TextView) findViewById(R.id.textView1);
				text.setTypeface(null, Typeface.NORMAL);
				text.setTextColor(Color.GRAY);
				//set the notification as seen in the backend
			}
		}); */
	}
	
	public void markRead(int id) {
		TextView text = (TextView) this.findViewById(id);
		text.setTypeface(null, Typeface.NORMAL);
		text.setTextColor(Color.GRAY);
	}
}
