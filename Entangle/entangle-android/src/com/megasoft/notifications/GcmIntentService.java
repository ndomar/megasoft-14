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
	 * this will be called after handling the notification, it's main role is to
	 * decide which activity to call
	 * 
	 * @param intent
	 * @author Shaban
	 */
	private void sendNotification(Intent intent) {
		Bundle bundle = intent.getExtras();

		String title = bundle.getString("title"), body = bundle
				.getString("body");

		int type = -1, notificationId = -1;
		if (intent.getExtras().getString("type") != null)
			type = Integer.parseInt(bundle.getString("type"));

		if (intent.getExtras().getString("notificationId") != null)
			notificationId = Integer.parseInt(bundle
					.getString("notificationId"));

		Intent dest = null;
		switch (type) {
		case 0:
			dest = new Intent(this, MainActivity.class);
			break;
		case 1:
			break;
		case 2:
			dest = fetchOfferData(bundle);
			break;
		case 3:
			dest = fetchRequestData(bundle);
			break;
		case 4:
			dest = fetchOfferData(bundle);
			break;
		case 5:
			dest = fetchRequestData(bundle);
			break;
		case 6:
			dest = fetchHomeActivity(bundle);
			break;
		case 7:
			dest = fetchRequestData(bundle);
			break;
		case 8:
			//ask salma
			dest = null;
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

	public Intent fetchRequestData(Bundle bundle) {
		SharedPreferences settings = getSharedPreferences(Config.SETTING, 0);
		String sessionId = settings.getString(Config.SESSION_ID, "");

		int requestId = -1, tangleId = -1;
		String tangleName = "";
		if (bundle.getString("requestId") != null)
			requestId = Integer.parseInt(bundle.getString("requestId"));
		Log.i("GCM", "request:" + requestId);
		if (bundle.getString("tangleId") != null)
			tangleId = Integer.parseInt(bundle.getString("tangleId"));
		if (bundle.getString("tangleName") != null)
			tangleName = bundle.getString("tangleName");
		Intent dest = new Intent(getApplicationContext(), RequestActivity.class);
		dest.putExtra("tangleId", tangleId);
		dest.putExtra("tangleName", tangleName);
		dest.putExtra("sessionId", sessionId);
		dest.putExtra("requestId", requestId);
		return dest;
	}

	public Intent fetchOfferData(Bundle bundle) {
		Intent dest = new Intent(this, OfferActivity.class);
		int offerId = -1;
		if (bundle.getString("offerId") != null) {
			offerId = Integer.parseInt(bundle.getString("offerId"));
		}
		dest.putExtra("offerId", offerId);
		return dest;
	}

	public Intent fetchHomeActivity(Bundle bundle) {
		Intent dest = new Intent(this, HomeActivity.class);
		int tangleId = -1;
		if (bundle.getString("tangleId") != null)
			tangleId = Integer.parseInt(bundle.getString("tangleId"));
		dest.putExtra("tangleId", tangleId);
		dest.putExtra("tab", "stream");
		return dest;
	}
}