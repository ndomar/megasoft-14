package com.megasoft.notifications;

import android.app.IntentService;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;

import com.megasoft.config.Config;
import com.megasoft.entangle.HomeActivity;
import com.megasoft.entangle.MainActivity;
import com.megasoft.entangle.OfferActivity;
import com.megasoft.entangle.R;
import com.megasoft.entangle.RequestActivity;
import com.megasoft.entangle.R.drawable;

/**
 * this classes are here just for testing purposes. this will be called whenever
 * a message arrives to gcm broadcast receiver
 * 
 * @author Shaban
 */
public class GcmIntentService extends IntentService {
	static int NotificationCounter = 0;
	PendingIntent contentIntent;
	
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

	/**
	 * this will be called after handling the notification
	 * 
	 * @param intent
	 * @author Shaban
	 */
	private void sendNotification(Intent intent) {
		String title = intent.getExtras().getString("title");
		String body = intent.getExtras().getString("body");
		int type = -1;
		int notificationId = -1;
		if (intent.getExtras().getString("type") != null)
			type = Integer.parseInt(intent.getExtras().getString("type"));
		if (intent.getExtras().getString("notificationId") != null)
			notificationId = Integer.parseInt(intent.getExtras().getString(
					"notificationId"));

		Intent dest = new Intent(this, MainActivity.class);
		SharedPreferences settings = getSharedPreferences(Config.SETTING, 0);
		String sessionId = settings.getString(Config.SESSION_ID, "");

		int requestId = -1;
		int tangleId = -1;
		String tangleName = "";

		switch (type) {
		case 0:
			dest = new Intent(this, MainActivity.class);
			break;
		case 1:
			break;
		case 2:
			dest = new Intent(this, OfferActivity.class);
			int offerId = -1;
			if (intent.getExtras().getString("offerId") != null) {
				offerId = Integer.parseInt(intent.getExtras().getString(
						"offerId"));
			}
			dest.putExtra("offerId", offerId);
			break;
		case 3:
			if (intent.getExtras().getString("requestId") != null)
				requestId = Integer.parseInt(intent.getExtras().getString(
						"requestId"));
			Log.i("GCM", "request:" + requestId);
			if (intent.getExtras().getString("tangleId") != null)
				tangleId = Integer.parseInt(intent.getExtras().getString(
						"tangleId"));
			if (intent.getExtras().getString("tangleName") != null)
				tangleName = intent.getExtras().getString("tangleName");
			dest = new Intent(getApplicationContext(), RequestActivity.class);
			dest.putExtra("tangleId", tangleId);
			dest.putExtra("tangleName", tangleName);
			dest.putExtra("sessionId", sessionId);
			dest.putExtra("requestId", requestId);
			break;
		case 4:
			dest = new Intent(this, OfferActivity.class);
			offerId = -1;
			if (intent.getExtras().getString("offerId") != null) {
				offerId = Integer.parseInt(intent.getExtras().getString(
						"offerId"));
			}
			dest.putExtra("offerId", offerId);
			break;
		case 5:
			break;
		case 6:
			dest = new Intent(this, HomeActivity.class);
			tangleId = -1;
			if (intent.getExtras().getString("tangleId") != null)
				tangleId = Integer.parseInt(intent.getExtras().getString(
						"tangleId"));
			dest.putExtra("tangleId", tangleId);
			dest.putExtra("tab", "stream");
			break;
		case 7:
			if (intent.getExtras().getString("requestId") != null)
				requestId = Integer.parseInt(intent.getExtras().getString(
						"requestId"));
			Log.i("GCM", "request:" + requestId);
			if (intent.getExtras().getString("tangleId") != null)
				tangleId = Integer.parseInt(intent.getExtras().getString(
						"tangleId"));
			if (intent.getExtras().getString("tangleName") != null)
				tangleName = intent.getExtras().getString("tangleName");
			dest = new Intent(getApplicationContext(), RequestActivity.class);
			dest.putExtra("tangleId", tangleId);
			dest.putExtra("tangleName", tangleName);
			dest.putExtra("sessionId", sessionId);
			dest.putExtra("requestId", requestId);
			break;
		}

		if (type != -1) {
			contentIntent = PendingIntent.getActivity(this, 0, dest, 0);
			Notification notification = new Notification.Builder(
					getApplicationContext()).setContentTitle(title)
					.setContentText(body)
					.setSmallIcon(R.drawable.entangle_logo)
					.setContentIntent(contentIntent).setAutoCancel(true)
					.build();
			NotificationManager notManager = (NotificationManager) getSystemService(NOTIFICATION_SERVICE);
			notManager.notify(NotificationCounter++, notification);
		}
	}
}