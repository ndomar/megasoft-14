package com.megasoft.entangle;

import java.io.IOException;
import java.util.concurrent.atomic.AtomicInteger;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.GooglePlayServicesUtil;
import com.google.android.gms.gcm.GoogleCloudMessaging;
import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;
import com.megasoft.utils.UI;

public class MainActivity extends Activity {

	private final static int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
	public static final String PROPERTY_REG_ID = "registration_id";
	String SENDER_ID = "87338614452";
	static final String TAG = "GCMDemo";
	GoogleCloudMessaging gcm;
	String regid;
	public static final String uri = "http://shaban.apiary-mock.com/gcm/register";
	TextView disp;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		disp = (TextView) findViewById(R.id.TVdisplay);
		register();
	}

	public void register() {
		if (checkPlayServices()) {
			gcm = GoogleCloudMessaging.getInstance(this);
			regid = getRegistrationId(getApplicationContext());
			if (regid.equals("")) {
				registerInBackground();
			}
		} else {
			Log.i(TAG, "no play services api found");
		}
	}

	@Override
	protected void onResume() {
		super.onResume();
		checkPlayServices();
	}

	/**
	 * Check the device to make sure it has the Google Play Services APK. If it
	 * doesn't, display a dialog that allows users to download the APK from the
	 * Google Play Store or enable it in the device's system settings.
	 */
	private boolean checkPlayServices() {
		int resultCode = GooglePlayServicesUtil
				.isGooglePlayServicesAvailable(this);
		if (resultCode != ConnectionResult.SUCCESS) {
			if (GooglePlayServicesUtil.isUserRecoverableError(resultCode)) {
				GooglePlayServicesUtil.getErrorDialog(resultCode, this,
						PLAY_SERVICES_RESOLUTION_REQUEST).show();
			} else {
				Log.i("ERROR", "This device is not supported.");

				finish();
			}
			return false;
		}
		return true;
	}

	private String getRegistrationId(Context context) {
		SharedPreferences prefs = getSharedPreferences(
				MainActivity.class.getSimpleName(), MODE_PRIVATE);
		String regiterationId = prefs.getString(PROPERTY_REG_ID, "");
		if (regiterationId.equals("")) {
			Log.i(TAG, "reg id not found");
		} else {
			Log.i(TAG, "reg id found");
		}
		Log.i(TAG, regiterationId);
		return regiterationId;
	}

	private void registerInBackground() {
		new AsyncTask<Void, Void, String>() {

			@Override
			protected String doInBackground(Void... params) {
				if (gcm == null)
					gcm = GoogleCloudMessaging
							.getInstance(getApplicationContext());
				try {
					regid = gcm.register(SENDER_ID);
				} catch (IOException e) {
					e.printStackTrace();
				}
				sendRegisterationId(regid);
				storeRegisteratinId(regid);
				return regid;
			}

			@Override
			protected void onPostExecute(String regid) {
				UI.makeToast(getApplicationContext(),
						"sucessfully registerd to GCM", Toast.LENGTH_LONG);
			}
		}.execute(null, null, null);
	}

	protected void sendRegisterationId(String regid) {

		JSONObject json = new JSONObject();
		try {
			json.put("regid", regid);
		} catch (JSONException e1) {
			e1.printStackTrace();
		}
		PostRequest req = new PostRequest(uri, json);
		req.addHeader("X-SESSION-ID", getSessionId());
		req.execute();
	}

	protected String getSessionId() {
		SharedPreferences prefs = getSharedPreferences("sessionIDPrefs",
				MODE_PRIVATE);
		return prefs.getString(Config.SESSION_ID, "");
	}

	protected void storeRegisteratinId(String regid) {
		SharedPreferences prefs = getSharedPreferences(
				MainActivity.class.getSimpleName(), MODE_PRIVATE);
		SharedPreferences.Editor editor = prefs.edit();
		editor.putString(PROPERTY_REG_ID, regid);
		editor.commit();
	}
}
