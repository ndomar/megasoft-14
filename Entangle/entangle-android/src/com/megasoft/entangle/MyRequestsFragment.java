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
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;
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

	private LinearLayout openRequests;

	private LinearLayout frozenRequests;

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
			String requesterButtonText = requesterName;
			String requestButtonText = requestBody;
			String requestPrice = "---";
			int status = request.getInt("status");
			if (request.get("price") != null
					&& !request.getString("price").equals("null"))
				requestPrice = "" + request.getInt("price");
			transaction = getFragmentManager().beginTransaction();
			StreamRequestFragment requestFragment = new StreamRequestFragment();
			switch (status) {
			case 0:
				requestFragment = OpenRequestFragment.createInstance(requestId,
						userId, requestButtonText, requesterButtonText,
						requestPrice, requestOffersCount, getTangleId(),
						getTangleName());
				break;
			case 1:
				requestFragment = FrozenRequestFragment.createInstance(
						requestId, userId, requestButtonText,
						requesterButtonText, requestPrice, requestOffersCount,
						getTangleId(), getTangleName());
				break;
			case 2:
				requestFragment = ClosedRequestFragment.createInstance(
						requestId, userId, requestButtonText,
						requesterButtonText, requestPrice, requestOffersCount,
						getTangleId(), getTangleName());
				break;
			default:
				break;
			}
			addRequestFragment(status, requestFragment);
			transaction.commit();
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

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
