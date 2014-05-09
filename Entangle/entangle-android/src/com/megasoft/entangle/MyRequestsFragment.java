package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentTransaction;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.megasoft.config.Config;
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

	/**
	 * The Layout that contains the open requests
	 */
	private LinearLayout openRequests;

	/**
	 * The Layout that contains the frozen requests
	 */
	private LinearLayout frozenRequests;

	/**
	 * The Layout that contains the closed requests
	 */
	private LinearLayout closedRequests;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		View view = inflater.inflate(R.layout.template_my_requests, container,
				false);

		tangleId = getArguments().getInt("tangleId");
		tangleName = getArguments().getString("tangleName");
		sendRequest(rootResource + "/tangle/" + tangleId + "/user/requests");
		openRequests = (LinearLayout) view.findViewById(R.id.openRequests);
		frozenRequests = (LinearLayout) view.findViewById(R.id.frozenRequests);
		closedRequests = (LinearLayout) view.findViewById(R.id.closedRequests);
		return view;
	}

	@Override
	public void onAttach(Activity activity) {
		this.activity = (FragmentActivity) activity;
		super.onAttach(activity);
	}

	/**
	 * This method is used to set the layout of the stream dynamically according
	 * to response of the request of getting all the requests
	 * 
	 * @param res
	 *            , is the response string of the stream request
	 * @author HebaAamer
	 */
	private void setTheLayout(String res) {
		try {
			JSONObject response = new JSONObject(res);
			if (response != null) {
				int count = response.getInt("count");
				JSONArray requestArray = response.getJSONArray("requests");
				if (count > 0 && requestArray != null) {
					cleanTheLayouts();
					for (int i = 0; i < count && i < requestArray.length(); i++) {
						JSONObject request = requestArray.getJSONObject(i);
						if (request != null) {
							addRequest(request);
						}
					}
				} else {
					Toast.makeText(
							activity.getBaseContext(),
							"Sorry, There is no requests with the specified options",
							Toast.LENGTH_LONG).show();
				}
			}
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to remove all the views in the different layouts
	 * 
	 * @author HebaAamer
	 */
	private void cleanTheLayouts() {
		openRequests.removeAllViews();
		frozenRequests.removeAllViews();
		closedRequests.removeAllViews();
	}

	/**
	 * This method is used to add specific request which is
	 * StreamRequestFragment to the layout of the stream
	 * 
	 * @param request
	 *            , is the request to be added in the layout
	 * @author HebaAamer
	 */
	@SuppressLint("NewApi")
	private void addRequest(JSONObject request) {
		try {
			int userId = request.getInt("userId");
			String requesterName = request.getString("username");
			int requestId = request.getInt("id");
			String requestBody = request.getString("description");
			String requestOffersCount = "" + request.getInt("offersCount");
			if (request.getInt("offersCount") == 0) {
				requestOffersCount = "";
			}
			String requesterButtonText = requesterName;
			String requestButtonText = requestBody;
			String requestPrice = "---";
			int status = request.getInt("status");
			if (request.get("price") != null
					&& !request.getString("price").equals("null"))
				requestPrice = "" + request.getInt("price");
			createFragment(status, requestId, userId, requestButtonText,
					requesterButtonText, requestPrice, requestOffersCount);
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	/**
	 * This method is used to create a fragment with passed parameters
	 * 
	 * @param status
	 *            , is the status of the request
	 * @param requestId
	 *            , is the id of the request
	 * @param userId
	 *            , is the id of the requester
	 * @param requestButtonText
	 *            , is the description of the request
	 * @param requesterButtonText
	 *            , is the name of the requester
	 * @param requestPrice
	 *            , is the price of the request
	 * @param requestOffersCount
	 *            , is the number of offers in that request
	 * @author HebaAamer
	 */
	private void createFragment(int status, int requestId, int userId,
			String requestButtonText, String requesterButtonText,
			String requestPrice, String requestOffersCount) {
		transaction = getFragmentManager().beginTransaction();
		StreamRequestFragment requestFragment = StreamRequestFragment
				.createInstance(requestId, userId, requestButtonText,
						requesterButtonText, requestPrice, requestOffersCount,
						getTangleId(), getTangleName());
		addRequestFragment(status, requestFragment);
		transaction.commit();
	}

	/**
	 * This method is used to put the request in its specific layout
	 * 
	 * @param status
	 *            , is the status of the request
	 * @param requestFragment
	 *            , is fragment to be added to the view
	 * 
	 * @author HebaAamer
	 */
	private void addRequestFragment(int status,
			StreamRequestFragment requestFragment) {
		switch (status) {
		case 0:
			transaction.add(R.id.openRequests, requestFragment);
			break;
		case 1:
			transaction.add(R.id.frozenRequests, requestFragment);
			break;
		case 2:
			transaction.add(R.id.closedRequests, requestFragment);
			break;
		default:
			break;
		}
	}

	/**
	 * This method is used to send a get request to get requests of certain user
	 * 
	 * @param url
	 *            , is the URL to which the request is going to be sent
	 * @author HebaAamer
	 */
	public void sendRequest(final String url) {
		sessionId = activity.getSharedPreferences(Config.SETTING, 0).getString(
				Config.SESSION_ID, "");
		GetRequest getStream = new GetRequest(url) {
			protected void onPostExecute(String res) {
				if (!this.hasError() && res != null) {
					cleanTheLayouts();
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
	 * @author HebaAamer
	 */
	private String getTangleName() {
		return tangleName;
	}

	/**
	 * This is a getter method used to get the session id of the user
	 * 
	 * @return session id
	 * @author HebaAamer
	 */
	private String getSessionId() {
		return sessionId;
	}

	/**
	 * This is a getter method used to get the tangle id
	 * 
	 * @return tangle id
	 * @author HebaAamer
	 */
	private int getTangleId() {
		return tangleId;
	}
}
