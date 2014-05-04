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


}
