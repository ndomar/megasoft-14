package com.megasoft.entangle;

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
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;

public class MyOffersFragment extends Fragment {

	/**
	 * The FragmentActivity that calls that Fragment
	 */
	private FragmentActivity activity;

	/**
	 * The View of the Fragment
	 */
	private View view;

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

	/**
	 * The Layout that holds the pending offers
	 */
	private LinearLayout pendingOffers;

	/**
	 * The Layout that holds the done offers
	 */
	private LinearLayout doneOffers;

	/**
	 * The Layout that holds the accepted offers
	 */
	private LinearLayout acceptedOffers;

	/**
	 * The Layout that holds the failed offers
	 */
	private LinearLayout failedOffers;

	/**
	 * The Layout that holds tha rejected offers
	 */
	private LinearLayout rejectedOffers;

	/**
	 * This method is called when the activity starts , it sets the attributes
	 * and redirections of all the views in this activity
	 * 
	 * @param savedInstanceState
	 *            , is the passed bundle from the previous activity
	 * @author HebaAamer
	 */

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater.inflate(R.layout.template_my_offers, container, false);
		tangleId = getArguments().getInt("tangleId");
		tangleName = getArguments().getString("tangleName");
		sendRequest(rootResource + "/tangle/" + tangleId + "/user/offers");
		setAttributes();
		return view;
	}

	private void setAttributes() {
		pendingOffers = (LinearLayout) view.findViewById(R.id.pendingOffers);
		doneOffers = (LinearLayout) view.findViewById(R.id.doneOffers);
		acceptedOffers = (LinearLayout) view.findViewById(R.id.acceptedOffers);
		failedOffers = (LinearLayout) view.findViewById(R.id.failedOffers);
		rejectedOffers = (LinearLayout) view.findViewById(R.id.rejectedOffers);
	}

	
}