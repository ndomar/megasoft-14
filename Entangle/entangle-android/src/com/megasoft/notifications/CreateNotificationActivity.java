package com.megasoft.notifications;

import com.megasoft.entangle.R;

import android.net.Uri;

import android.os.Bundle;

import android.app.Activity;

import android.app.NotificationManager;

import android.app.PendingIntent;

import android.app.TaskStackBuilder;

import android.content.Context;

import android.content.Intent;

import android.support.v4.app.NotificationCompat;

import android.view.View;

import android.widget.Button;

public class CreateNotificationActivity extends Activity {

	private NotificationManager myNotificationManager;

	private int notificationIdOne = 111;

	private int notificationIdTwo = 112;

	private int numMessagesOne = 0;

	private int numMessagesTwo = 0;

	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.main);

		Button notOneBtn = (Button) findViewById(R.id.notificationOne);

		notOneBtn.setOnClickListener(new View.OnClickListener() {

			public void onClick(View view) {

				displayNotificationOne();

			}

		});

		Button notTwoBtn = (Button) findViewById(R.id.notificationTwo);

		notTwoBtn.setOnClickListener(new View.OnClickListener() {

			public void onClick(View view) {

				displayNotificationTwo();

			}

		});

	}

	protected void displayNotificationOne() {

		// Invoking the default notification service

		NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(
				this);

		mBuilder.setContentTitle("New Message with explicit intent");

		mBuilder.setContentText("New message from javacodegeeks received");

		mBuilder.setTicker("Explicit: New Message Received!");

		mBuilder.setSmallIcon(R.drawable.ic_launcher);

		// Increase notification number every time a new notification arrives

		mBuilder.setNumber(++numMessagesOne);

		// Creates an explicit intent for an Activity in your app

		Intent resultIntent = new Intent(this, NotificationOne.class);

		resultIntent.putExtra("notificationId", notificationIdOne);

		// This ensures that navigating backward from the Activity leads out of
		// the app to Home page

		TaskStackBuilder stackBuilder = TaskStackBuilder.create(this);

		// Adds the back stack for the Intent

		stackBuilder.addParentStack(NotificationOne.class);

		// Adds the Intent that starts the Activity to the top of the stack

		stackBuilder.addNextIntent(resultIntent);

		PendingIntent resultPendingIntent =

		stackBuilder.getPendingIntent(

		0,

		PendingIntent.FLAG_ONE_SHOT // can only be used once

				);

		// start the activity when the user clicks the notification text

		mBuilder.setContentIntent(resultPendingIntent);

		myNotificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

		// pass the Notification object to the system

		myNotificationManager.notify(notificationIdOne, mBuilder.build());

	}

	protected void displayNotificationTwo() {

		// Invoking the default notification service

		NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(
				this);

		mBuilder.setContentTitle("New Message with implicit intent");

		mBuilder.setContentText("New message from javacodegeeks received...");

		mBuilder.setTicker("Implicit: New Message Received!");

		mBuilder.setSmallIcon(R.drawable.ic_launcher);

		NotificationCompat.InboxStyle inboxStyle = new NotificationCompat.InboxStyle();

		String[] events = new String[3];

		events[0] = new String("1) Message for implicit intent");

		events[1] = new String("2) big view Notification");

		events[2] = new String("3) from javacodegeeks!");

		// Sets a title for the Inbox style big view

		inboxStyle.setBigContentTitle("More Details:");

		// Moves events into the big view

		for (int i = 0; i < events.length; i++) {

			inboxStyle.addLine(events[i]);

		}

		mBuilder.setStyle(inboxStyle);

		// Increase notification number every time a new notification arrives

		mBuilder.setNumber(++numMessagesTwo);

		// When the user presses the notification, it is auto-removed

		mBuilder.setAutoCancel(true);

		// Creates an implicit intent

		Intent resultIntent = new Intent(
				"com.example.javacodegeeks.TEL_INTENT",

				Uri.parse("tel:123456789"));

		resultIntent.putExtra("from", "javacodegeeks");

		TaskStackBuilder stackBuilder = TaskStackBuilder.create(this);

		stackBuilder.addParentStack(NotificationTwo.class);

		stackBuilder.addNextIntent(resultIntent);

		PendingIntent resultPendingIntent =

		stackBuilder.getPendingIntent(

		0,

		PendingIntent.FLAG_ONE_SHOT

		);

		mBuilder.setContentIntent(resultPendingIntent);

		myNotificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

		myNotificationManager.notify(notificationIdTwo, mBuilder.build());

	}
}