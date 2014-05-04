package com.megasoft.entangle;

import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.FilteringFragment;
import com.megasoft.entangle.R;
import com.megasoft.entangle.StreamRequestFragment;
import com.megasoft.requests.GetRequest;

public class MyRequestsFragment extends Fragment {

	/**
	 * The FragmentActivity that calls that fragment
	 */
	private FragmentActivity activity;

	/**
	 * The domain to which the requests are sent
	 */
	private String rootResource = Config.API_BASE_URL_SERVER;

	/**
	 * The tangle id to which this stream belongs
	 */
	private int tangleId;

	/**
	 * The tangle name to which this stream belongs
	 */
	private String tangleName;

	/**
	 * The session id of the user
	 */
	private String sessionId;

	/**
	 * The FragmentTransaction that handles adding the fragments to the activity
	 */
	private FragmentTransaction transaction;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		View view = inflater
				.inflate(R.layout.activity_tangle, container, false);

		tangleId = getArguments().getInt("tangleId");
		tangleName = getArguments().getString("tangleName");
		
		sendRequest(rootResource + "/tangle/" + tangleId + "/userRequests");
		return view;
	}

	@Override
	public void onAttach(Activity activity) {
		this.activity = (FragmentActivity) activity;
		super.onAttach(activity);
	}
	
	private void setTheLayout(String res) {
		
	}
	
	/**
	 * This method is used to send a get request to get requests of certain user
	 * 
	 * @param url
	 *            , is the URL to which the request is going to be sent
	 */
	public void sendRequest(final String url) {
		sessionId = activity.getSharedPreferences(Config.SETTING, 0).getString(
				Config.SESSION_ID, "");
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					LinearLayout layout = (LinearLayout) activity
							.findViewById(R.id.streamLayout);
					layout.removeAllViews();
					setTheLayout(res);
				} else {
					Toast.makeText(activity.getBaseContext(),
							"Sorry, There is a problem in loading the stream",
							Toast.LENGTH_LONG).show();
				}
			}
		};
		getStream.addHeader(Config.API_SESSION_ID, getSessionId());
		getStream.execute();
	}

	/**
	 * This is a getter method used to get the tangle name
	 * 
	 * @return tangle name
	 */
	private String getTangleName() {
		return tangleName;
	}

	/**
	 * This is a getter method used to get the session id of the user
	 * 
	 * @return session id
	 */
	private String getSessionId() {
		return sessionId;
	}

	/**
	 * This is a getter method used to get the tangle id
	 * 
	 * @return tangle id
	 */
	private int getTangleId() {
		return tangleId;
	}
}
