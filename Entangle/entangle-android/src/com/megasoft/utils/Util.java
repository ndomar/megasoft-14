package com.megasoft.utils;

import java.util.concurrent.ExecutionException;

import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.SharedPreferences;
import android.widget.Toast;

import com.megasoft.entangle.R;
import com.megasoft.requests.PostRequest;

public class Util {

	static final String TAG = "GCMDemo";

	/**
	 * this checks if there is user logged in. it searches the shared
	 * preferences to get the sessionID
	 * 
	 * @param context
	 *            get the application context
	 * @return false when there is no user logged in true otherwise.
	 */
	public static boolean isUserLoggedIn(Context context) {
		SharedPreferences pref = context.getSharedPreferences(context
				.getResources().getString(R.string.PREFERENCE_FILE_KEY),
				Context.MODE_PRIVATE);
		String sessionID = pref.getString("sessionID", null);
		if (sessionID != null)
			return true;
		return false;
	}

	/**
	 * this is a template for any message to be sent in the application
	 * 
	 * @param userID
	 *            recipient ID
	 * @param message
	 *            message body
	 * @param postURl
	 *            post request url
	 */
	public static void sendUserMessage(Context context, int userID,
			String message, String postURl) {
		JSONObject obj = new JSONObject();
		try {
			obj.put("userID", userID);
			obj.put("message", message);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		PostRequest req = new PostRequest(postURl);
		req.setBody(obj);
		req.addHeader("X-SESSION-ID", "helloWorld");

		JSONObject response = new JSONObject();
		String text = "";
		String x;
		try {
			x = req.execute().get();
			if (x != null) {
				response = new JSONObject(req.execute().get());
				text = response.getString("message");
			}
		} catch (InterruptedException e) {
			e.printStackTrace();
		} catch (ExecutionException e) {
			e.printStackTrace();
		} catch (JSONException e) {
			e.printStackTrace();
		}
		if (req.getStatusCode() == 200) {
			UI.makeToast(context, text, Toast.LENGTH_LONG);
		} else {
			UI.makeToast(context, "couldn't connect to server",
					Toast.LENGTH_LONG);
			// there is a bug with buildDialog to be resolved later
			// UI.buildDialog(context, "Internet error",
			// "Internet not working");
		}
	}
}
