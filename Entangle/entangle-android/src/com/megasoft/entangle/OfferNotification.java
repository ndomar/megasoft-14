package com.megasoft.entangle;

import java.util.concurrent.ExecutionException;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;

import android.app.Activity;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.TaskStackBuilder;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.NotificationCompat;
import android.widget.TextView;

public class OfferNotification extends Activity {
	
	String notification = "";
	String offerer = "";
	String description = "";
	String request = "";
	String amount = "";
	String notificationID = "12432";
	final String link = "Click here to view";
	
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.offer_notification);
		
		//Get request to retrive the notification data
		
		GetRequest getRequest = new GetRequest("http://entangle2.apiary.io/notification/1");
		getRequest.addHeader("sessionID" , "testest");
		getRequest.execute("http://entangle2.apiary.io/notification/1");
		
		try {
			JSONObject json = new JSONObject(getRequest.get());
			description = json.getString("description");
			offerer = json.getString("offerer");
			request = json.getString("request");
			amount = json.getString("Amount");
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
		notification = offerer + " has offered " + amount + " on your " + request + link;
		TextView txt = (TextView) findViewById(R.id.textView1);
		txt.setText(notification);
		createNotification();
	}
	
	/**
	 * This is used to create the notification
	 */
	
	public void createNotification() {
		
		NotificationCompat.Builder mBuilder = 
				new NotificationCompat.Builder(this)
				//.setSmallIcon(RESULT_OK)
				.setContentTitle("Entangle:You got a new offer!")
				.setContentText(notification);
		

		Intent resultIntent = new Intent(this , Notification.class);
		
		TaskStackBuilder sb = TaskStackBuilder.create(this);
		sb.addParentStack(OfferNotification.class);
		sb.addNextIntent(resultIntent);
		
		PendingIntent pIntent =
		        sb.getPendingIntent(
		            0,
		            PendingIntent.FLAG_UPDATE_CURRENT
		        );
		mBuilder.setContentIntent(pIntent);
		
		NotificationManager notificationMgr = 
				(NotificationManager) getSystemService(NOTIFICATION_SERVICE);
		
		int ID = Integer.parseInt(notificationID);
		notificationMgr.notify(ID , mBuilder.build());
	}

}
