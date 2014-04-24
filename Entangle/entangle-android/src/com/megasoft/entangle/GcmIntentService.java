package com.megasoft.entangle;

import android.app.IntentService;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.NotificationCompat;

import com.megasoft.notifications.CreateNotificationActivity;

public class GcmIntentService extends IntentService {
	public static final int NOTIFICATION_ID = 1;
	private NotificationManager mNotificationManager;
	NotificationCompat.Builder builder;
	String TAG = "Notification";

	public GcmIntentService() {
		super("GcmIntentService");
	}

	@Override
	protected void onHandleIntent(Intent intent) {
		Bundle extras = intent.getExtras();
		if (!extras.isEmpty()) {
			sendNotification(intent);
		}
		GcmBroadcastReceiver.completeWakefulIntent(intent);
	}

	private void sendNotification(Intent intent) {
		mNotificationManager = (NotificationManager) this
				.getSystemService(Context.NOTIFICATION_SERVICE);
		String title = intent.getExtras().getString("title");
		String body = intent.getExtras().getString("body");

		PendingIntent contentIntent = PendingIntent.getActivity(this, 0,
				new Intent(this, MainActivity.class), 0);

		NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(
				this).setSmallIcon(R.drawable.common_signin_btn_icon_dark)
				.setContentTitle("GCM Notification")
				.setStyle(new NotificationCompat.BigTextStyle().bigText(title))
				.setContentText(body);
		mBuilder.setContentIntent(contentIntent);
		mNotificationManager.notify(NOTIFICATION_ID, mBuilder.build());
	}
}