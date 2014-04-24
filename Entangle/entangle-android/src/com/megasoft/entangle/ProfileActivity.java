package com.megasoft.entangle;

import com.megasoft.requests.DeleteRequest;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.AlertDialog.Builder;
import android.app.Dialog;
import android.content.DialogInterface;
import android.view.View;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.ImageRequest;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

/**
 * Views a user's profile given his user Id and the tangle Id that redirected to
 * the profile
 * 
 * @author Almgohar
 */
public class ProfileActivity extends Activity {

	/**
	 * The Button that redirects to the EditProfileActivity
	 */
	private Button edit;

	/**
	 * The button that allows the user to leave the current tangle
	 */
	private Button leave;
	/**
	 * The TextView that holds the user's name
	 */
	private TextView name;

	/**
	 * The TextView that holds the user's description
	 */
	private TextView description;

	/**
	 * The TextView that holds the user's credit/balance
	 */
	private TextView balance;

	/**
	 * The TextView that holds the user's birth date
	 */
	private TextView birthDate;

	/**
	 * The ImageView that indicates whether the user is verified
	 */
	private ImageView verifiedView;

	/**
	 * The ImageView that holds the user's profile picture
	 */
	private ImageView profilePictureView;

	/**
	 * The LinearLayout that holds the user's transactions
	 */
	private LinearLayout transactionsLayout;

	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;

	/**
	 * The id of the logged in user
	 */
	private int loggedInId;

	/**
	 * The tangle Id from which we were redirected
	 */
	private int tangleId;

	/**
	 * The user Id whose profile we want to view
	 */
	private int userId;

	/**
	 * The session Id of the logged in user
	 */

	private String sessionId;

	/**
	 * This is method is invoked by the button of leave tangle when it is
	 * clicked
	 * 
	 * @author Almgohar, HebaAamer
	 */
	@SuppressWarnings("deprecation")
	private void leaveTangle() {
		this.showDialog(0);
	}

	/**
	 * This method is called when showDialog(int) method is called and it is
	 * responsible for creating a dialog to make sure that the user wants to
	 * leave the tangle
	 * 
	 * @author HebaAamer
	 */
	@Override
	protected Dialog onCreateDialog(int dialogId) {
		Builder dialogBuilder = new AlertDialog.Builder(this);
		dialogBuilder.setTitle("Leaving the tangle");
		dialogBuilder
				.setMessage("Are you sure you want to leave this tangle ?");
		dialogBuilder.setPositiveButton("Yes",
				new DialogInterface.OnClickListener() {

					@Override
					public void onClick(DialogInterface dialog, int which) {
						sendLeaveRequest();
						dialog.dismiss();
					}
				});
		dialogBuilder.setNegativeButton("NO",
				new DialogInterface.OnClickListener() {

					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
					}
				});
		return dialogBuilder.create();
	}

	/**
	 * This method is used to send the request of leaving the tangle and handles
	 * different responses
	 * 
	 * @author HebaAamer
	 */
	private void sendLeaveRequest() {
		DeleteRequest leaveRequest = new DeleteRequest(
				"http://entangle2.apiary-mock.com/tangle/" + tangleId + "/user") {
			public void onPostExecute(String response) {
				if (getStatusCode() == 204) {
					Toast.makeText(getBaseContext(),
							"You left the tangle successfully",
							Toast.LENGTH_LONG).show();
					// redirect to the tangles stream Activity
					Intent newIntent = new Intent(getBaseContext(),
							MainActivity.class);
					newIntent.putExtra("userId", userId);
					// getIntent().addFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);
					// ProfileActivity.this.
					// newIntent.addFlags(Intent.)
					startActivity(newIntent);

				} else if (getStatusCode() == 403) {
					Toast.makeText(getBaseContext(),
							"Sorry, you are not allowed to leave the tangle",
							Toast.LENGTH_LONG).show();
				} else {
					Toast.makeText(
							getBaseContext(),
							"Sorry, problem happened while leaving the tangle. Try again later",
							Toast.LENGTH_LONG).show();
				}
			}

		};
		leaveRequest.addHeader("X-SESSION-ID", sessionId);
		leaveRequest.execute();
	}

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_profile);

		Intent intent = getIntent();
		this.settings = getSharedPreferences(Config.SETTING, 0);

		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.loggedInId = settings.getInt(Config.USER_ID, -1);
		this.tangleId = intent.getIntExtra("tangle id", -1);
		this.userId = intent.getIntExtra("user id", -1);

		viewProfile();

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		getMenuInflater().inflate(R.menu.profile, menu);
		return true;

	}

	/**
	 * Initialize all views to link them to the XML views calls the
	 * ViewInformation() method
	 * 
	 * @author Almgohar
	 */
	public void viewProfile() {
		edit = (Button) findViewById(R.id.EditProfile);
		leave = (Button) findViewById(R.id.LeaveTangle);
		name = (TextView) findViewById(R.id.nameView);
		balance = (TextView) findViewById(R.id.balanceView);
		birthDate = (TextView) findViewById(R.id.birthdateView);
		description = (TextView) findViewById(R.id.descriptionView);
		verifiedView = (ImageView) findViewById(R.id.verifiedView);
		profilePictureView = (ImageView) findViewById(R.id.profileImage);
		transactionsLayout = (LinearLayout) this
				.findViewById(R.id.transactions_layout);

		viewInformation();
	}

	/**
	 * Creates a JSon request asking for the required information Retrieves the
	 * required information from the JSon response
	 * 
	 * @author Almgohar
	 */
	public void viewInformation() {
		String link = "http://entangle2.apiary-mock.com/tangle/" + tangleId
				+ "/user/" + userId + "/profile";
		GetRequest request = new GetRequest(link) {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					try {
						JSONObject jSon;
						jSon = new JSONObject(response);
						JSONArray transactions = jSon
								.getJSONArray("transactions");
						JSONObject information = jSon
								.getJSONObject("information");
						viewTransactions(transactions);
						name.setText(information.getString("name"));
						description.setText("Description: "
								+ information.getString("Description"));
						balance.setText("Credit: "
								+ information.getString("balance") + " points");
						birthDate.setText("Birthdate: "
								+ information.getString("birthdate"));
						viewProfilePicture(information.getString("picture URL"));
						boolean verified = information.getBoolean("verified");
						if (verified) {
							verifiedView.setVisibility(View.VISIBLE);
						}
						if (loggedInId == userId) {
							edit.setVisibility(View.VISIBLE);
							leave.setVisibility(View.VISIBLE);

							edit.setOnClickListener(new View.OnClickListener() {
								@Override
								public void onClick(View v) {
									goToEditProfile();
								}
							});
							leave.setOnClickListener(new View.OnClickListener() {
								@Override
								public void onClick(View v) {
									leaveTangle();

								}
							});
						}
					} catch (JSONException e) {
						e.printStackTrace();
					}
				} else {
					Toast toast = Toast.makeText(getApplicationContext(),
							"Some error happened.", Toast.LENGTH_SHORT);
					toast.show();
				}
			}
		};
		request.addHeader("X-SESSION-ID", this.sessionId);
		request.execute();
	}

	/**
	 * Gets the user's transactions from a JSONArray and views them
	 * 
	 * @param JSONArray
	 *            transactions
	 * @author Almgohar
	 */
	public void viewTransactions(JSONArray transactions) {
		TextView title = new TextView(this);
		title.setText("Transactions: ");
		title.setTextSize(20);
		transactionsLayout.addView(title);
		for (int i = 0; i < transactions.length(); i++) {
			JSONObject object;
			try {
				object = transactions.getJSONObject(i);
				TextView transaction = new TextView(this);
				final int offerId = object.getInt("offerId");
				String requester = object.getString("requesterName");
				String request = object.getString("requestDescription");
				String amount = object.getString("amount");
				transaction.setText("Requester: " + requester + '\n'
						+ "Request: " + request + '\n' + "Amount: " + amount);

				transaction.setOnClickListener(new View.OnClickListener() {
					@Override
					public void onClick(View v) {
						goToOffer(offerId);
					}
				});

				transactionsLayout.addView(transaction);

			} catch (JSONException e) {
				e.printStackTrace();
			}
		}
	}

	/**
	 * Views the user's profile picture
	 * 
	 * @param String
	 *            imageURL
	 * @author Almgohar
	 */
	public void viewProfilePicture(String imageURL) {
		ImageRequest image = new ImageRequest(profilePictureView);
		image.execute(imageURL);
	}

	/**
	 * Redirects to the EditProfileActivity
	 * 
	 * @author Almgohar
	 */
	public void goToEditProfile() {
		Intent editProfile = new Intent(this, EditProfileActivity.class);
		editProfile.putExtra("user id", loggedInId);
		startActivity(editProfile);
	}

	/**
	 * Redirects to OfferActivity
	 * 
	 * @author Almgohar
	 */
	public void goToOffer(int offerId) {
		Intent offer = new Intent(this, OfferActivity.class);
		offer.putExtra("offer id", offerId);
		startActivity(offer);
	}
}
