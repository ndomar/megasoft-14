package com.megasoft.entangle.acceptPendingInvitation;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.OfferEntryFragment;
import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;
import com.megasoft.requests.PutRequest;

/**
 * The Activity that handles the pending tangle invitations.
 * 
 * @author MohamedBassem
 * 
 */
public class ManagePendingInvitationFragment extends Fragment {

	/**
	 * The id of the current tangle
	 */
	private int tangleId;

	/**
	 * The preferences instance
	 */
	SharedPreferences settings;

	/**
	 * The session id of the currently logged in user
	 */
	String sessionId;

	/**
	 * The number of pending invitations
	 */
	int pendingInvitationCount;

	private View view;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		this.view = inflater.inflate(
				R.layout.fragment_manage_pending_invitation, container, false);
		tangleId = getArguments().getInt("tangleId", 1);
		this.settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		Button resetButton = (Button) view.findViewById(R.id.reset_tangle);
		resetButton.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				reset();

			}
		});
		Button button = (Button) view
				.findViewById(R.id.manage_pending_invitation_refresh);
		button.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				fetchData();
			}
		});
		fetchData();
		return view;
	}

	/**
	 * The method responsible for sending the GET request to the endpoint to
	 * fetch the invitations
	 * 
	 * @author MohamedBassem
	 */
	private void fetchData() {
		if (!isNetworkAvailable()) {
			showErrorToast();
			return;
		}
		GetRequest request = new GetRequest(Config.API_BASE_URL + "/tangle/"
				+ tangleId + "/pending-invitations") {
			public void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					showData(response);
				} else {
					showErrorToast();
				}
			}
		};
		request.addHeader("X-SESSION-ID", sessionId);
		request.execute();

	}

	/**
	 * The method responsible for adding the pending invitation fetched from the
	 * api to the layout
	 * 
	 * @param response
	 *            , The string containing the JSON response from the API
	 * @author MohamedBassem
	 */
	public void showData(String response) {
		((LinearLayout) view.findViewById(R.id.pending_invitation_layout))
				.removeAllViews();
		JSONObject json = null;

		try {
			json = new JSONObject(response);
			JSONArray jsonArray = json.getJSONArray("pending-invitations");
			FragmentTransaction transaction = getActivity()
					.getSupportFragmentManager().beginTransaction();
			for (int i = 0; i < jsonArray.length(); i++) {
				JSONObject pendingInvitation = jsonArray.getJSONObject(i);

				String text = pendingInvitation.getString("inviter")
						+ " invited ";
				if (pendingInvitation.getString("invitee").equals("null")) {
					text += "the new member ( "
							+ pendingInvitation.getString("email") + " )";
				} else {
					text += pendingInvitation.getString("invitee") + " ( "
							+ pendingInvitation.getString("email") + " )";
				}

				PendingInvitationFragment requestFragment = PendingInvitationFragment
						.createInstance(pendingInvitation.getInt("id"), text,
								this);
				transaction
						.add(R.id.pending_invitation_layout, requestFragment);
			}

			transaction.commit();
			pendingInvitationCount = jsonArray.length();
			checkEmptyPendingInvitations();
		} catch (JSONException e) {
			e.printStackTrace();
		}

	}

	/**
	 * Triggered by the fragment to remove itself from the layout when approved
	 * or rejected
	 * 
	 * @param fragment
	 *            , the fragment to be removed
	 * @author MohamedBassem
	 */
	public void removeFragment(PendingInvitationFragment fragment) {
		getFragmentManager().beginTransaction().remove(fragment).commit();
		pendingInvitationCount--;
		checkEmptyPendingInvitations();
	}

	/**
	 * Used to check whether there is any pending invitations , if not , an
	 * appropriate message appears
	 * 
	 * @author MohamedBassem
	 */
	private void checkEmptyPendingInvitations() {
		if (pendingInvitationCount == 0) {
			view.findViewById(R.id.pending_invitation_no_pending)
					.setVisibility(View.VISIBLE);
		} else {
			view.findViewById(R.id.pending_invitation_no_pending)
					.setVisibility(View.GONE);
		}
	}

	/**
	 * Checks the Internet connectivity.
	 * 
	 * @return true if there is an Internet connection , false otherwise
	 */
	private boolean isNetworkAvailable() {
		ConnectivityManager connectivityManager = (ConnectivityManager) getActivity()
				.getSystemService(Context.CONNECTIVITY_SERVICE);
		NetworkInfo activeNetworkInfo = connectivityManager
				.getActiveNetworkInfo();
		return activeNetworkInfo != null && activeNetworkInfo.isConnected();
	}

	/**
	 * Shows a something went wrong toast
	 */
	private void showErrorToast() {
		Toast.makeText(getActivity(), "Sorry , Something went wrong.",
				Toast.LENGTH_SHORT).show();
	}

	/**
	 * method to reset tangle by sending json object
	 * 
	 * @param none
	 * @return none
	 * @author Salma Khaled
	 */
	public void reset() {
		DialogInterface.OnClickListener dialogClickListener = new DialogInterface.OnClickListener() {

			public void onClick(DialogInterface dialog, int which) {
				switch (which) {
				case DialogInterface.BUTTON_POSITIVE: {
					JSONObject json = new JSONObject();

					PutRequest request = new PutRequest(
							Config.API_BASE_URL_SERVER + "/tangle/" + tangleId
									+ "/reset") {
						protected void onPostExecute(String response) {
							if (this.getStatusCode() == 200) {
								Toast.makeText(getActivity(),
										"Tangle is reset !", Toast.LENGTH_SHORT)
										.show();
							} else {
								Toast.makeText(getActivity(),
										"Error, Can not create request",
										Toast.LENGTH_SHORT).show();
							}
						}
					};
					request.addHeader(Config.API_SESSION_ID, sessionId);
					request.setBody(json);
					request.execute();
					break;
				}
				case DialogInterface.BUTTON_NEGATIVE: {
					dialog.dismiss();
					break;
				}
				}
			}
		};

		AlertDialog.Builder builder = new AlertDialog.Builder(getActivity());
		builder.setMessage("Are you sure?")
				.setPositiveButton("Yes", dialogClickListener)
				.setNegativeButton("No", dialogClickListener).show();
	}
	/**
	 * method that makes a get request to get claims
	 * @param none
	 * @return none
	 * @author sak93
	 */
	public void getClaims() {
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		GetRequest request = new GetRequest(Config.API_BASE_URL_SERVER) {
			protected void onPostExecute(String response) {
				try {

					if (this.getStatusCode() == 200) {
						JSONObject json = new JSONObject(response);
						/*
						 * requestLayout.removeAllViewsInLayout();
						 * offersLayout.removeAllViewsInLayout();
						 */
						addClaims(json);
					} else {

					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};
		request.addHeader(Config.API_SESSION_ID, sessionId);
		request.execute();

	}
	/**
	 * method to add claim details
	 * @param JSONObject json
	 * @return none
	 * @author sak93
	 */
	public void addClaims(JSONObject json) throws JSONException {
		JSONArray claims = (JSONArray) json.get("offers");
		if (claims.length() == 0) {
			// findViewById(R.id.view_request_offer_header).setVisibility(View.INVISIBLE);
		} else {
			// findViewById(R.id.view_request_offer_header).setVisibility(View.VISIBLE);
		}

		for (int i = 0; i < claims.length(); i++) {
			JSONObject claim = claims.getJSONObject(i);
			OfferEntryFragment offerFragmet = new OfferEntryFragment();
			Bundle args = new Bundle();
			args.putInt("claimId", Integer.parseInt(claim.getString("id")));
			args.putString("price", claim.getString("offerPrice"));
			String message = "Claim #" + claim.getString("claimId") + ": "
					+ claim.getString("Claimer") + "made a clame on offer"
					+ claim.getString("offerId");

			offerFragmet.setArguments(args);

			getSupportFragmentManager().beginTransaction()
					.add(R.id.offer_entries_layout, offerFragmet).commit();
		}

		// if (myRequest == true) {
		// Button deleteRequest = new Button(this);
		// deleteRequest.setText("Delete");
		// layout.addView(deleteRequest);
		// }
	}

}