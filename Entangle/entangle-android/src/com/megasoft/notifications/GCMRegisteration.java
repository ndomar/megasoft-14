package com.megasoft.notifications;

import java.io.IOException;
import java.util.concurrent.ExecutionException;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.IntentService;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.util.Log;

import com.google.android.gms.gcm.GoogleCloudMessaging;
import com.megasoft.config.Config;
import com.megasoft.entangle.MainActivity;
import com.megasoft.requests.PostRequest;

/**
 * this class is responsible for registering the user to google cloud messaging.
 * it runs in the background  
 * @author mohamed
 */
public class GCMRegisteration extends IntentService {

	/**
	 * entangle Google Cloud Messaging project number
	 */
	String SENDER_ID = "87338614452";
	/**
	 * TAG name for debugging
	 */
	static final String TAG = "GCM";
	/**
	 * Google Cloud Messaging instance
	 */
	GoogleCloudMessaging gcm;

	/**
	 * registration ID of current session
	 */
	String regid;

	/**
	 * URI for registration
	 */

	public static final String uri = Config.API_BASE_URL
			+ "/notification/register";
	
	
	public GCMRegisteration() {
		super("GCMRegisteration");
		// TODO Auto-generated constructor stub
	}

	@Override
	protected void onHandleIntent(Intent arg0) {
		// TODO Auto-generated method stub
		register();
	}
	
	/**
	 * this method checks if a user is has GCM registration id. if no it asks
	 * for one from registerInBackground
	 * 
	 * @param None
	 * @return None
	 * @author Shaban
	 */
	public void register() {
		regid = getRegistrationId(getApplicationContext());
		if (regid.equals(""))
			registerInBackground();

	}

	/**
	 * checks shared prefs if the user already has a registration ID
	 * 
	 * @param context
	 * @return registration ID String
	 * @author Shaban
	 */
	private String getRegistrationId(Context context) {
		SharedPreferences prefs = getSharedPreferences(
				Config.GCM_DATA, MODE_PRIVATE);
		String registrationId = prefs.getString(Config.PROPERTY_REG_ID, "");
		if (registrationId.equals("")) {
			Log.i(TAG, "reg id not found");
		} else {
			Log.i(TAG, "reg id found");
		}
		Log.i(TAG, registrationId);
		return registrationId;
	}

	/**
	 * this function register a user then sends registration id with session Id
	 * to server registration uri
	 * 
	 * @param None
	 * @return None
	 * @author Shaban
	 */
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
				return regid;
			}

			@Override
			protected void onPostExecute(String regid) {
				sendRegisterationId(regid);
				Log.i(TAG, regid);
				storeRegistrationId(regid);
			}
		}.execute(null, null, null);
	}

	/**
	 * this sends a post request to server registration uri to attach this key
	 * to the session id
	 * 
	 * @return None
	 * @param regid
	 * @author Shaban
	 */
	protected void sendRegisterationId(String regid) {

		JSONObject json = new JSONObject();
		try {
			json.put("regid", regid);
		} catch (JSONException e1) {
			e1.printStackTrace();
		}
		PostRequest req = new PostRequest(uri) {

			@Override
			protected void onPostExecute(String result) {
				// TODO Auto-generated method stub
				try {
					String res = this.get();
				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (ExecutionException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		};
		req.setBody(json);
		req.addHeader("X-SESSION-ID", "" + getSessionId());
		req.addHeader("Content-Type", "application/json");
		req.execute();
	}

	/**
	 * gets session Id from shared prefs
	 * 
	 * @param None
	 * @return session IDString
	 * @author Shaban
	 */
	protected String getSessionId() {
		SharedPreferences prefs = getSharedPreferences(Config.SETTING, 0);
		return prefs.getString(Config.SESSION_ID, "");
	}

	/**
	 * stores registration Id in shared prefs
	 * 
	 * @param regid
	 * @return None
	 * @author Shaban
	 */
	protected void storeRegistrationId(String regid) {
		SharedPreferences prefs = getSharedPreferences(
				Config.GCM_DATA, MODE_PRIVATE);
		SharedPreferences.Editor editor = prefs.edit();
		editor.putString(Config.PROPERTY_REG_ID, regid);
		editor.commit();
	}
}
