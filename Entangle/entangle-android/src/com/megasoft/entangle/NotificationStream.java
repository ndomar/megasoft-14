package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.view.Menu;
import android.view.View;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.Toast;

public class NotificationStream extends FragmentActivity {
	
	/**
	 * The session Id
	 */
	private String sessionId;
	
	/**
	 * The user Id
	 */
	private int userId;
	
	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_notification);
		
		
		generate();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

	public void generate() {
		
		viewNotificationsTest();
		/*
		String link = Config.API_BASE_URL + "/" + userId + "/notifications";
		
		GetRequest request = new GetRequest(link) {
			@Override
			protected void onPostExecute(String response) {
				
				if (this.getStatusCode() == 200) {
					try {
						JSONObject json = new JSONObject(response);
						viewNotifications(json.getJSONArray("notifications"));
						viewNotificationsTest();
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
					Toast toast = Toast
							.makeText(
									getApplicationContext(),
									this.getErrorMessage() + " "
											+ this.getStatusCode(),
									Toast.LENGTH_SHORT);
					toast.show();
				}
			}
		}; */
	}
	
	public void viewNotificationsTest() {
		
		LinearLayout notificationsArea = ((LinearLayout) findViewById(R.id.notification_stream));
		notificationsArea.removeAllViews();
		notificationsArea.setVisibility(View.VISIBLE);
		
		NotificationStreamFragment fragment = new NotificationStreamFragment();
		fragment.setData(1, 2, false , "Notification 1 description", "offer notification");
		getSupportFragmentManager().beginTransaction().add(R.id.notification_stream, fragment).commit();
		NotificationStreamFragment fragment2 = new NotificationStreamFragment();
		fragment2.setData(2, 2,true , "Notification 2 description", "change notification");
		getSupportFragmentManager().beginTransaction().add(R.id.notification_stream, fragment2).commit();
		NotificationStreamFragment fragment3 = new NotificationStreamFragment();
		fragment3.setData(2, 2,true , "Notification 2 description", "change notification");
		getSupportFragmentManager().beginTransaction().add(R.id.notification_stream, fragment3).commit();
		NotificationStreamFragment fragment4 = new NotificationStreamFragment();
		fragment4.setData(2, 2,true , "Notification 2 description", "change notification");
		getSupportFragmentManager().beginTransaction().add(R.id.notification_stream, fragment4).commit();
		NotificationStreamFragment fragment5 = new NotificationStreamFragment();
		fragment5.setData(2, 2,true , "Notification 2 description", "change notification");
		getSupportFragmentManager().beginTransaction().add(R.id.notification_stream, fragment5).commit();
		
		final ScrollView scrollView = (ScrollView) findViewById(R.id.scroll);
		scrollView.postDelayed(new Runnable() {

			@Override
			public void run() {
				scrollView.fullScroll(ScrollView.FOCUS_UP);
			}
		}, 500);
		
	}
	
	public void viewNotifications(JSONArray notifications) {
		
		for(int i = 0; i < notifications.length();i++) {
			JSONObject notification;
			try {
				notification = notifications.getJSONObject(i);
				NotificationStreamFragment fragment = new NotificationStreamFragment();
				String notificationDescription = notification.getString("description");
				String notificationType = notification.getString("type");
				int notificationId = notification.getInt("notificationId");
				fragment.setData(notificationId, userId , true , notificationDescription, notificationType);
				getSupportFragmentManager().beginTransaction().add(R.layout.activity_notification, fragment).commit();
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			
		}
	}
	
}
