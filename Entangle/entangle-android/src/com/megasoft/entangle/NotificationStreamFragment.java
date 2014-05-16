package com.megasoft.entangle;

import com.megasoft.config.Config;
import com.megasoft.requests.PutRequest;
import android.R.color;
import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;

/**
 * This is a fragment class where each fragment is a notification
 * 
 * @author Mohamed Ayman
 */
public class NotificationStreamFragment extends Fragment {

	private View view;
	private int notificationId;
	private String notificationDescription;
	private boolean seen;
	private String notificationDate;
	private String linkTo;
	private String linkToId;
	private FragmentActivity activity;
	private int tangleId;

	/**
	 * Setting the attributes of the fragment
	 * 
	 * @param notificationId
	 * @param seen
	 * @param notificationDescription
	 * @param date
	 * @param link
	 */
	public void setData(int notificationId, boolean seen,
			String notificationDescription, String date, String link , int tangleId) {
		this.notificationId = notificationId;
		this.notificationDescription = notificationDescription;
		this.seen = seen;
		this.notificationDate = date;
		this.linkTo = link;
		this.tangleId = tangleId;
	}

	/**
	 * It manages the view of the fragment
	 * 
	 * @author Mohamed Ayman
	 */
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		view = inflater.inflate(R.layout.fragment_notification_stream,
				container, false);
		String[] link = linkTo.split("=");
		linkTo = link[0];
		linkToId = link[1];
		TextView notificationDescriptionView = (TextView) view
				.findViewById(R.id.notificationDescription);
		notificationDescriptionView.setTextSize(20);
		TextView notificationDateView = (TextView) view
				.findViewById(R.id.notificationDate);
		notificationDateView.setText(notificationDate);
		if (!this.seen) {
			notificationDescriptionView.setTypeface(null, Typeface.BOLD);
		} else {
			notificationDescriptionView.setTextColor(Color.GRAY);
		}
		notificationDescriptionView.setText(this.notificationDescription);

		notificationDescriptionView
				.setOnClickListener(new View.OnClickListener() {

					@Override
					public void onClick(View v) {
						// TODO Auto-generated method stub
						setSeen();

						if (linkTo.equals("offer")) {
							goToOffer();
						} else if (linkTo.equals("request")) {
							goToRequest();
						} else {
							goToHome();
						}
					}
				});

		return view;
	}

	public int getNotificationId() {
		return this.notificationId;
	}

	/**
	 * This method is used to set the notification status as seen
	 * 
	 * @author Mohamed Ayman
	 */
	public void setSeen() {

		PutRequest putRequest = new PutRequest(Config.API_BASE_URL
				+ "/notification/" + notificationId + "/set-seen");
		putRequest.addHeader("X-SESSION-ID", Config.SESSION_ID);
		putRequest.execute();
	}

	/**
	 * Go to the offer activity
	 */
	public void goToOffer() {
		Intent intent = new Intent(getActivity().getBaseContext(),
				OfferActivity.class);
		intent.putExtra("offerID", Integer.parseInt(linkToId));
		startActivity(intent);
	}

	/**
	 * Go to the request activity
	 */
	public void goToRequest() {
		Intent intent = new Intent(getActivity().getBaseContext(),
				RequestActivity.class);
		intent.putExtra("requestId", Integer.parseInt(linkToId));
		intent.putExtra("tangleId", this.tangleId);
		startActivity(intent);
	}

	/**
	 * Go to the home activity
	 */
	public void goToHome() {
		Intent intent = new Intent(getActivity().getBaseContext(),
				HomeActivity.class);
		startActivity(intent);
	}

	@Override
	public void onAttach(Activity activity) {
		this.activity = (FragmentActivity) activity;
		super.onAttach(this.activity);
	}
}