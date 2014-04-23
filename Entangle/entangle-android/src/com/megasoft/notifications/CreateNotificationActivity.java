package com.megasoft.notifications;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;

import android.net.Uri;

import android.os.Bundle;

import android.app.Activity;

import android.app.NotificationManager;

import android.app.PendingIntent;

import android.app.TaskStackBuilder;
import android.app.DownloadManager.Request;

import android.content.Context;

import android.content.Intent;

import android.support.v4.app.NotificationCompat;

import android.util.Log;
import android.view.View;

import android.widget.Button;
import android.widget.Toast;

public class CreateNotificationActivity extends Activity {

	private NotificationManager myNotificationManager;
	private NotificationCompat.Builder mBuilder;
	private int notificationIdOne = 111;
	private int numMessagesOne = 0;
	private JSONObject json;
	private String requesterName = null;
	private String tangleName = null;
	private String requestId;
	private String requestStatus = null;
	private String requestDesc = null;
	private Intent resultIntent;
	private PendingIntent resultPendingIntent;

	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.main);

		Button notOneBtn = (Button) findViewById(R.id.notificationOne);

		notOneBtn.setOnClickListener(new View.OnClickListener() {

			public void onClick(View view) {

				GetRequest request = new GetRequest(
						"http://getchooseanotheroffer.apiary-mock.com/tangle/request/5") {
					protected void onPostExecute(String response) {
						try {

							json = new JSONObject(response);
							requesterName = json.getString("requester-name");
							tangleName = json.getString("tangle-name");
							requestId = (String) json.getString("request-id");
							requestDesc = json.getString("request-desc");
							requestStatus = json.getString("request-status");

						} catch (Exception e) {

							System.out.println("error");

						}
						displayNotificationOne();
					}
				};

				request.addHeader("X-SESSION-ID", "user1");
				request.execute();

			}

		});

	}

	protected void displayNotificationOne() {

		// Invoking the default notification service

		mBuilder = new NotificationCompat.Builder(this);

		mBuilder.setContentTitle("New Entangle Notification");

		mBuilder.setContentText("New Notification From " + requesterName);

		mBuilder.setTicker("New Entangle Notification");

		mBuilder.setSmallIcon(R.drawable.ic_launcher);

		// Increase notification number every time a new notification arrives

		mBuilder.setNumber(++numMessagesOne);

		// Creates an explicit intent for the activity , then adds extra data so
		// we can retrieve and use them in the notification activity

		resultIntent = new Intent(this, NotificationOne.class);
		resultIntent.putExtra("notificationId", notificationIdOne);
		resultIntent.putExtra("tangleName", tangleName);
		resultIntent.putExtra("requesterName", requesterName);
		resultIntent.putExtra("requestId", requestId);
		resultIntent.putExtra("requestStatus", requestStatus);
		resultIntent.putExtra("requestDesc", requestDesc);

		// This ensures that navigating backward from the Activity leads out of
		// the app to Home page

		TaskStackBuilder stackBuilder = TaskStackBuilder.create(this);

		// Adds the back stack for the Intent

		stackBuilder.addParentStack(NotificationOne.class);

		// Adds the Intent that starts the Activity to the top of the stack

		stackBuilder.addNextIntent(resultIntent);

		resultPendingIntent =

		stackBuilder.getPendingIntent(

		0,

		PendingIntent.FLAG_ONE_SHOT // can only be used once

				);

		// start the activity when the user clicks the notification text

		mBuilder.setContentIntent(resultPendingIntent);

		myNotificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

		myNotificationManager.notify(notificationIdOne, mBuilder.build());

	}
}