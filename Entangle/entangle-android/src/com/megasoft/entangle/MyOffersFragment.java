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
import com.megasoft.utils.UI;

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
	private String rootResource = Config.API_BASE_URL;

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

	private boolean isDestroyed;

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

	@Override
	public void onAttach(Activity activity) {

		this.activity = (FragmentActivity) activity;
		super.onAttach(activity);
	}

	/**
	 * This method is used to initialize the different layouts of the view
	 * 
	 * @author HebaAamer
	 */
	private void setAttributes() {
		pendingOffers = (LinearLayout) view.findViewById(R.id.pendingOffers);
		doneOffers = (LinearLayout) view.findViewById(R.id.doneOffers);
		acceptedOffers = (LinearLayout) view.findViewById(R.id.acceptedOffers);
		failedOffers = (LinearLayout) view.findViewById(R.id.failedOffers);
		rejectedOffers = (LinearLayout) view.findViewById(R.id.rejectedOffers);
	}

	/**
	 * This method is used to send a get request to get all the offers
	 * 
	 * @param url
	 *            , is the URL to which the request is going to be sent
	 * 
	 * @author HebaAamer
	 */
	public void sendRequest(final String url) {
		sessionId = activity.getSharedPreferences(Config.SETTING, 0).getString(
				Config.SESSION_ID, "");
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if(isDestroyed){
					return;
				}
				if (!this.hasError() && res != null) {
					removeLayoutViews();
					setTheLayout(res);
				} else {
					UI.makeToast(activity.getBaseContext(),
							"Sorry, There is a problem in loading your offers",
							Toast.LENGTH_LONG);
				}
			}
		};
		getStream.addHeader(Config.API_SESSION_ID, getSessionId());
		getStream.execute();
	}

	/**
	 * This method is used to set the layout of the view dynamically according
	 * to response of the request
	 * 
	 * @param res
	 *            , is the response string of the stream request
	 */
	private void setTheLayout(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray offerArray = response.getJSONArray("offers");
				if (count > 0 && offerArray != null) {
					removeLayoutViews();
					for (int i = 0; i < count && i < offerArray.length(); i++) {
						JSONObject offer = offerArray.getJSONObject(i);
						if (offer != null) {
							addOffer(offer);
						}
					}
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to add specific offer which is EntryOfferFragment to
	 * the view
	 * 
	 * @param offer
	 *            , is the offer to be added in the layout
	 */
	@SuppressLint("NewApi")
	private void addOffer(JSONObject offer) {
		try {
			int userId = offer.getInt("userId");
			String offererName = offer.getString("username");
			int offerId = offer.getInt("id");
			String offerBody = offer.getString("description");
			int status = offer.getInt("status");
			String offerPrice = "---";
			if (offer.get("price") != null
					&& !offer.getString("price").equals("null"))
				offerPrice = "" + offer.getInt("price");
			addOfferEntry(userId, offererName, offerId, offerBody, offerPrice,
					status);
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to create an EntryOfferFragment with the specified
	 * parameters
	 * 
	 * @param userId
	 *            , is the id of the offerer
	 * @param offererName
	 *            , is the name of the offerer
	 * @param offerId
	 *            , is the id of the offer
	 * @param offerBody
	 *            , is the description of the offer
	 * @param offerPrice
	 *            , is the price of the offer
	 * @param status
	 *            , is the status of the offer
	 * @author HebaAamer
	 */
	private void addOfferEntry(int userId, String offererName, int offerId,
			String offerBody, String offerPrice, int status) {
		transaction = getFragmentManager().beginTransaction();
		EntryOfferFragment offerFragment = new EntryOfferFragment();
		Bundle args = new Bundle();
		args.putInt("offerId", offerId);
		args.putString("requestedPrice", offerPrice);
		args.putString("description", offerBody);
		args.putString("offerer", offererName);
		args.putInt("userId", userId);
		args.putInt("tangleId", tangleId);
		args.putString("tangleName", tangleName);
		args.putString("offererAvatar", activity.getSharedPreferences(Config.SETTING, 0).getString(Config.PROFILE_IMAGE, null));
		offerFragment.setArguments(args);
		putInPlace(status, offerFragment);
		transaction.commit();
	}

	/**
	 * This method is used to put each offer in its corresponding layout
	 * 
	 * @param status
	 *            , is the status of the offer
	 * @param offer
	 *            , is the offer fragment
	 * @author HebaAamer
	 */
	private void putInPlace(int status, EntryOfferFragment offer) {
		switch (status) {
		case 0:
			transaction.add(R.id.pendingOffers, offer);
			break;
		case 1:
			transaction.add(R.id.doneOffers, offer);
			break;
		case 2:
			transaction.add(R.id.acceptedOffers, offer);
			break;
		case 3:
			transaction.add(R.id.failedOffers, offer);
			break;
		case 4:
			transaction.add(R.id.rejectedOffers, offer);
			break;
		default:
			break;
		}
	}

	/**
	 * This is a getter method used to get the tangle id
	 * 
	 * @return tangle id
	 * @author HebaAamer
	 */
	public int getTangleId() {
		return tangleId;
	}

	/**
	 * This method is a getter method used to get the name of the tangle
	 * 
	 * @return tangle name
	 * @author HebaAamer
	 */
	public String getTangleName() {
		return tangleName;
	}

	/**
	 * This is a getter method used to get the session id of the user
	 * 
	 * @return session id
	 * @author HebaAamer
	 */
	public String getSessionId() {
		return sessionId;
	}

	/**
	 * This method is used to clear the different layouts in the view
	 * 
	 * @author HebaAamer
	 */
	private void removeLayoutViews() {
		pendingOffers.removeAllViews();
		doneOffers.removeAllViews();
		acceptedOffers.removeAllViews();
		failedOffers.removeAllViews();
		rejectedOffers.removeAllViews();
	}
	
	public void onPause(){
		super.onPause();
		isDestroyed = true;
	}

}